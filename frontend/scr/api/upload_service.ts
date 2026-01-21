import { apiRequest } from './client';

export const uploadAssignmentFile = async (contractId: number, file: File) => {
  // We use FormData because standard JSON cannot carry physical files
  const formData = new FormData();
  formData.append('file', file);
  formData.append('contract_id', contractId.toString());

  const token = localStorage.getItem('token');

  const response = await fetch(`http://localhost:5000/upload/${contractId}`, {
    method: 'POST',
    headers: {
      'Authorization': `Bearer ${token}`
      // Note: Don't set 'Content-Type' manually; the browser does it for FormData
    },
    body: formData
  });

  if (!response.ok) {
    throw new Error('Upload failed');
  }

  return response.json();
};
