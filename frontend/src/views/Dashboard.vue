<template>
  <div>
    <div class="section-header">
      <h2>Recent Expenses</h2>
      <div class="action-buttons">
        <button @click="handleExportCSV" class="action-button">⬇ Export CSV</button>
        <button @click="exportPDF" class="action-button">⬇ Download PDF</button>
        <router-link to="/expenses" class="view-all">View All</router-link>
      </div>
    </div>

    <div class="dashboard">
      <!-- Stats Cards -->
       
      <div class="stats-cards">
        <div class="stat-card total-card">
          <h3>Total Expenses</h3>
          <p>${{ Number(totalExpenses).toFixed(2) }}</p>
          <span class="stat-subtitle">All Time</span>
        </div>

        <div class="stat-card monthly-card">
          <h3>This Month</h3>
          <p>${{ Number(monthlyExpenses).toFixed(2) }}</p>
          <span class="stat-subtitle">{{ currentMonth }}</span>
        </div>

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
        <DataTable :items="recentExpenses" :columns="expenseColumns">
          <template #amount="{ value }">${{ Number(value).toFixed(2) }}</template>
          <template #expense_date="{ value }">{{ formatDate(value) }}</template>
          <template #group_id="{ value }">{{ getGroupName(value) }}</template>
          <template #actions="{ item }">
            <button @click="handleDeleteExpense(item.id)" class="delete-btn">Delete</button>
          </template>
          <template #empty>
            <p>No expenses recorded yet</p>
            <router-link to="/expenses" class="add-link">Add Expense</router-link>
          </template>
        </DataTable>
      </div>

      <!-- Groups Table -->
      <div class="table-section">
        <div class="section-header">
          <h2>Your Groups</h2>
          <router-link to="/groups" class="view-all">View All</router-link>
        </div>
        <DataTable :items="groups" :columns="groupColumns">
          <template #total="{ item }">${{ Number(getGroupTotal(item.id)).toFixed(2) }}</template>
          <template #actions="{ item }">
            <button @click="handleDeleteGroup(item.id)" class="delete-btn">Delete</button>
          </template>
          <template #empty>
            <p>No groups created yet</p>
            <router-link to="/groups" class="add-link">Create Group</router-link>
          </template>
        </DataTable>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed, onMounted, ref, watch } from 'vue';
import { useExpenseStore } from '@/stores/Expense';
import { useGroupStore } from '@/stores/Group';
import { Chart, registerables } from 'chart.js';
import api from '@/services/api';
import DataTable from '@/components/DataTable.vue';
import '@/assets/css/dashboard.css';
import { exportCSV } from "@/utils/exportCSV";

Chart.register(...registerables);

const expenseStore = useExpenseStore();
const groupStore = useGroupStore();
const chartCanvas = ref(null);
let chartInstance = null;

const expenseColumns = [
  { key: 'expense_name', label: 'Title' },
  { key: 'amount', label: 'Amount' },
  { key: 'expense_date', label: 'Date' },
  { key: 'group_id', label: 'Group' },
  { key: 'actions', label: 'Actions' }
];

const groupColumns = [
  { key: 'group_name', label: 'Name' },
  { key: 'total', label: 'Total Amount' },
  { key: 'actions', label: 'Actions' }
];

onMounted(() => {
  expenseStore.fetchExpenses();
  groupStore.fetchGroups();
});

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

watch(() => expenseStore.expenses, updateChart, { deep: true });

const prepareChartData = () => {
  const expenses = expenseStore.expenses;

  const labels = [];
  const data = [];

  expenses.forEach((e) => {
    const groupName = getGroupName(e.group_id) || 'Uncategorized';
    const label = `${groupName} - ${e.expense_name}`;
    labels.push(label);
    data.push(Number(e.amount));
  });

  const colors = [
    '#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF',
    '#FF9F40', '#8AC24A', '#607D8B', '#E91E63', '#00BCD4'
  ];
  while (colors.length < labels.length) {
    colors.push('#'+Math.floor(Math.random()*16777215).toString(16)); // Random extra colors
  }

  return {
    labels,
    datasets: [{
      data,
      backgroundColor: colors.slice(0, labels.length),
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

const getGroupName = (groupId) => {
  if (!groupId) return '-';
  const group = groupStore.groups.find(g => g.id === groupId);
  return group ? group.group_name : 'Unknown';
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

const handleExportCSV = () => {
  exportCSV();
};

const exportPDF = async () => {
  try {
    const response = await api.get("/api/expenses/export-pdf", {
      responseType: 'blob',
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
