export function formatDate(dateString) {
  if (!dateString) return '';
  
  const options = { 
    year: 'numeric', 
    month: 'short', 
    day: 'numeric',
    timeZone: 'UTC' // Add this if you're having timezone issues
  };
  
  try {
    return new Date(dateString).toLocaleDateString(undefined, options);
  } catch (e) {
    console.error('Error formatting date:', e);
    return dateString; // Return the original string if formatting fails
  }
}