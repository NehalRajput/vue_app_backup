<template>
  <div class="section-header">
  <h2>Recent Expenses</h2>
  <div>
    <button @click="exportCSV" class="view-all">⬇ Download CSV</button>
    <button @click="exportPDF" class="view-all">⬇ Download PDF</button>
    <router-link to="/expenses" class="view-all">View All</router-link>
  
  </div>
</div>

  <div class="dashboard">
    <!-- Stats Cards -->
    <div class="stats-cards">
      <!-- Total Expenses -->
      <div class="stat-card total-card">
        <h3>Total Expenses</h3>
        <p>${{ Number(totalExpenses).toFixed(2) }}</p>
        <span class="stat-subtitle">All Time</span>
      </div>

      <!-- This Month -->
      <div class="stat-card monthly-card">
        <h3>This Month</h3>
        <p>${{ Number(monthlyExpenses).toFixed(2) }}</p>
        <span class="stat-subtitle">{{ currentMonth }}</span>
      </div>

      <!-- Highest Spending -->
      <div class="stat-card highest-card">
        <h3>Highest Spending</h3>
        <template v-if="highestExpense">
          ${{ Number(highestExpense.amount).toFixed(2) }}
        </template>
        <p v-else>No expenses</p>
        <span class="stat-subtitle">{{ currentMonth }}</span>
      </div>
    </div>

    <!-- Chart Section -->
    <div class="chart-section">
      <div class="section-header">
        <h2>Expense Distribution</h2>
      </div>
      <div class="chart-container">
        <canvas ref="chartCanvas"></canvas>
      </div>
    </div>

    <!-- Recent Expenses Table -->
    <div class="table-section">
      <div class="section-header">
        <h2>Recent Expenses</h2>
        <router-link to="/expenses" class="view-all">View All</router-link>
      </div>
      <div class="table-container">
        <table class="data-table">
          <thead>
            <tr>
              <th>Title</th>
              <th>Amount</th>
              <th>Date</th>
              <th>Group</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="e in recentExpenses" :key="e.id">
              <td>{{ e.expense_name }}</td>
              <td class="amount-cell">${{ Number(e.amount).toFixed(2) }}</td>
              <td>{{ formatDate(e.expense_date) }}</td>
              <td>{{ e.group_id || '-' }}</td>
              <td class="actions-cell">
                <button @click="handleDeleteExpense(e.id)" class="delete-btn">🔚</button>
              </td>
            </tr>
            <tr v-if="recentExpenses.length === 0">
              <td colspan="5" class="empty-table-message">
                <p>No expenses recorded yet</p>
                <router-link to="/expenses" class="add-link">Add Expense</router-link>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Groups Table -->
    <div class="table-section">
      <div class="section-header">
        <h2>Your Groups</h2>
        <router-link to="/groups" class="view-all">View All</router-link>
      </div>
      <div class="table-container">
        <table class="data-table">
          <thead>
            <tr>
              <th>Name</th>
              <th>Total Amount</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="g in groups" :key="g.id">
              <td>{{ g.group_name }}</td>
              <td class="amount-cell">${{ Number(getGroupTotal(g.id)).toFixed(2) }}</td>
              <td class="actions-cell">
                <button @click="handleDeleteGroup(g.id)" class="delete-btn">🔚</button>
              </td>
            </tr>
            <tr v-if="groups.length === 0">
              <td colspan="3" class="empty-table-message">
                <p>No groups created yet</p>
                <router-link to="/groups" class="add-link">Create Group</router-link>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed, onMounted, ref, watch } from 'vue';
import { useExpenseStore } from '@/stores/expense';
import { useGroupStore } from '@/stores/group';
import { Chart, registerables } from 'chart.js';
import api from '@/services/api';
Chart.register(...registerables);

const expenseStore = useExpenseStore();
const groupStore = useGroupStore();
const chartCanvas = ref(null);
let chartInstance = null;

onMounted(() => {
  expenseStore.fetchExpenses();
  groupStore.fetchGroups();
});

watch(() => expenseStore.expenses, () => {
  updateChart();
}, { deep: true });

const updateChart = () => {
  if (!chartCanvas.value) return;
  if (chartInstance) chartInstance.destroy();
  const ctx = chartCanvas.value.getContext('2d');
  const data = prepareChartData();

  chartInstance = new Chart(ctx, {
    type: 'pie',
    data,
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: {
          position: 'right',
          labels: {
            color: '#fff',
            font: {
              family: "'Courier New', monospace",
              size: 14
            }
          }
        },
        tooltip: {
          callbacks: {
            label(context) {
              const label = context.label || '';
              const value = context.raw || 0;
              const total = context.dataset.data.reduce((a, b) => a + b, 0);
              const percentage = Math.round((value / total) * 100);
              return `${label}: $${value.toFixed(2)} (${percentage}%)`;
            }
          },
          bodyFont: {
            family: "'Courier New', monospace",
            size: 14
          }
        }
      }
    }
  });
};

const prepareChartData = () => {
  const expenses = expenseStore.expenses;
  const groupedData = expenses.reduce((acc, e) => {
    const key = e.group_id || 'Uncategorized';
    acc[key] = (acc[key] || 0) + Number(e.amount);
    return acc;
  }, {});

  const sortedEntries = Object.entries(groupedData).sort((a, b) => b[1] - a[1]);
  let labels, data;
  if (sortedEntries.length > 6) {
    const top = sortedEntries.slice(0, 5);
    const others = sortedEntries.slice(5).reduce((s, [, v]) => s + v, 0);
    labels = [...top.map(([l]) => l), 'Others'];
    data = [...top.map(([, v]) => v), others];
  } else {
    labels = sortedEntries.map(([l]) => l);
    data = sortedEntries.map(([, v]) => v);
  }

  const colors = [
    '#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF',
    '#FF9F40', '#8AC24A', '#607D8B', '#E91E63', '#00BCD4'
  ].slice(0, labels.length);

  return {
    labels,
    datasets: [{
      data,
      backgroundColor: colors,
      borderWidth: 1,
      borderColor: '#333'
    }]
  };
};

const currentMonth = new Date().toLocaleString('default', { month: 'long', year: 'numeric' });

const totalExpenses = computed(() => expenseStore.expenses.reduce((s, e) => s + (Number(e.amount) || 0), 0));

const monthlyExpenses = computed(() => {
  const now = new Date(), m = now.getMonth(), y = now.getFullYear();
  return expenseStore.expenses
    .filter(e => {
      const d = new Date(e.expense_date);
      return d.getMonth() === m && d.getFullYear() === y;
    })
    .reduce((s, e) => s + (Number(e.amount) || 0), 0);
});

const highestExpense = computed(() => {
  const now = new Date(), m = now.getMonth(), y = now.getFullYear();
  const monthly = expenseStore.expenses
    .filter(e => {
      const d = new Date(e.expense_date);
      return d.getMonth() === m && d.getFullYear() === y;
    });
  if (!monthly.length) return null;
  return monthly.reduce((max, e) => Number(e.amount) > Number(max.amount) ? e : max);
});

const recentExpenses = computed(() => [...expenseStore.expenses].sort((a, b) => new Date(b.expense_date) - new Date(a.expense_date)).slice(0, 5));

const groups = computed(() => groupStore.groups);

const formatDate = d => d ? new Date(d).toLocaleDateString(undefined, { year: 'numeric', month: 'short', day: 'numeric' }) : '';

const getGroupTotal = (groupId) => {
  return expenseStore.expenses
    .filter(e => e.group_id === groupId)
    .reduce((s, e) => s + (Number(e.amount) || 0), 0);
};

const handleDeleteExpense = (id) => {
  if (confirm("Are you sure you want to delete this expense?")) {
    expenseStore.deleteExpense(id);
  }
};

const handleDeleteGroup = (id) => {
  if (confirm("Are you sure you want to delete this group?")) {
    groupStore.deleteGroup(id);
  }
};

const exportCSV = async () => {
  try {
    const response = await api.get("/api/expenses/export", {
      responseType: 'blob', // Important for binary files
      headers: {
        Authorization: `Bearer ${localStorage.getItem("token")}`
      }
    });

    const blob = new Blob([response.data], { type: 'text/csv' });
    const url = window.URL.createObjectURL(blob);

    const link = document.createElement("a");
    link.href = url;
    link.setAttribute("download", "expenses.csv");
    document.body.appendChild(link);
    link.click();
    link.remove();
  } catch (error) {
    console.error("CSV Export Failed:", error);
  }
};

const exportPDF = async () => {
  try {
    const response = await api.get("/api/expenses/export-pdf", {
      responseType: 'blob', // Important for binary files
      headers: {
        Authorization: `Bearer ${localStorage.getItem("token")}`
      }
    });

    const blob = new Blob([response.data], { type: 'application/pdf' });
    const url = window.URL.createObjectURL(blob);

    const link = document.createElement("a");
    link.href = url;
    link.setAttribute("download", "expenses.pdf");
    document.body.appendChild(link);
    link.click();
    link.remove();
  } catch (error) {
    console.error("PDF Export Failed:", error);
  }
};


</script>


<style scoped>
.dashboard {
  padding: 1.5rem;
  max-width: 1200px;
  margin: 0 auto;
  color: white;
  font-family: 'Courier New', monospace;
}

.stats-cards {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
  gap: 1rem;
  margin-bottom: 2rem;
}

.stat-card {
  background: #111;
  border-radius: 12px;
  padding: 1.5rem;
  color: white;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
  transition: transform 0.2s;
}

.stat-card:hover {
  transform: translateY(-5px);
}

.stat-card h3 {
  margin-top: 0;
  font-size: 1rem;
  color: #42b983;
}

.stat-card p {
  font-size: 1.8rem;
  margin: 0.5rem 0;
  font-weight: bold;
}

.stat-subtitle {
  font-size: 0.9rem;
  color: #aaa;
}

.chart-section {
  background: #111;
  border-radius: 12px;
  padding: 1.5rem;
  margin-bottom: 2rem;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

.chart-container {
  position: relative;
  height: 400px;
  width: 100%;
}

.section-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 1.5rem;
}

.section-header h2 {
  margin: 0;
  font-size: 1.5rem;
  color: white;
}

.view-all {
  color: #42b983;
  text-decoration: none;
  font-size: 1rem;
  font-weight: bold;
  padding: 0.5rem 1rem;
  border-radius: 6px;
  transition: all 0.2s;
}

.view-all:hover {
  background-color: rgba(66, 185, 131, 0.1);
  text-decoration: none;
}

.table-section {
  background: #111;
  border-radius: 12px;
  padding: 1.5rem;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
  margin-bottom: 2rem;
}

.table-container {
  overflow-x: auto;
}

.data-table {
  width: 100%;
  border-collapse: collapse;
  font-size: 1rem;
}

.data-table th {
  text-align: left;
  padding: 1rem;
  background-color: #222;
  color: #42b983;
  font-weight: bold;
  border-bottom: 2px solid #333;
}

.data-table td {
  padding: 1rem;
  border-bottom: 1px solid #333;
  vertical-align: middle;
}

.data-table tr:last-child td {
  border-bottom: none;
}

.data-table tr:hover {
  background-color: rgba(66, 185, 131, 0.05);
}

.amount-cell {
  font-weight: bold;
  color: #42b983;
}

.actions-cell {
  text-align: center;
  width: 60px;
}

.delete-btn {
  background: none;
  border: none;
  color: #e74c3c;
  cursor: pointer;
  padding: 0.5rem;
  font-size: 1.2rem;
  border-radius: 50%;
  transition: all 0.2s;
}

.delete-btn:hover {
  background-color: rgba(231, 76, 60, 0.1);
  transform: scale(1.1);
}

.empty-table-message {
  text-align: center;
  padding: 2rem;
  color: #666;
}

.empty-table-message p {
  margin-bottom: 1rem;
}

.add-link {
  display: inline-block;
  padding: 0.75rem 1.5rem;
  background-color: #42b983;
  color: white;
  border-radius: 6px;
  text-decoration: none;
  font-weight: bold;
  transition: all 0.2s;
}

.add-link:hover {
  background-color: #369f6b;
  transform: translateY(-2px);
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
}

@media (max-width: 768px) {
  .stats-cards {
    grid-template-columns: 1fr;
  }
  
  .data-table {
    font-size: 0.9rem;
  }
  
  .data-table th,
  .data-table td {
    padding: 0.75rem;
  }
  
  .chart-container {
    height: 300px;
  }
  
  .section-header h2 {
    font-size: 1.25rem;
  }
}
</style>