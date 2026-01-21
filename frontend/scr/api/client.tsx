const BASE_URL = "http://localhost:5000"; // Your Python Backend URL

export const apiRequest = async (endpoint: string, method: string, body?: any) => {
  const token = localStorage.getItem('token');
  
  const response = await fetch(`${BASE_URL}${endpoint}`, {
    method: method,
    headers: {
      'Content-Type': 'application/json',
      'Authorization': token ? `Bearer ${token}` : ''
    },
    body: body ? JSON.stringify(body) : null
  });

  if (!response.ok) {
    const error = await response.json();
    throw new Error(error.message || 'Something went wrong');
  }

  return response.json();
};
