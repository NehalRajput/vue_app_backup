import api from '@/services/api';

export const exportCSV = async () => {
  try {
    const response = await api.get("/api/expenses/export", {
      responseType: 'blob',
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
