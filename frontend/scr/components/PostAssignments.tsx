import React, { useState } from 'react';

const PostAssignment = () => {
  const [formData, setFormData] = useState({ title: '', description: '', budget: 0 });

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    
    // Sending the assignment data to your Python Backend
    const response = await fetch('http://localhost:5000/assignments', {
      method: 'POST',
      headers: { 
        'Content-Type': 'application/json',
        'Authorization': `Bearer ${localStorage.getItem('token')}` // The JWT from your Auth Service
      },
      body: JSON.stringify(formData)
    });

    if (response.ok) {
      alert("Assignment posted successfully!");
    }
  };

  return (
    <form onSubmit={handleSubmit} className="p-4 bg-white rounded shadow">
      <input 
        type="text" 
        placeholder="Assignment Title" 
        onChange={(e) => setFormData({...formData, title: e.target.value})} 
      />
      <textarea 
        placeholder="Describe the task..." 
        onChange={(e) => setFormData({...formData, description: e.target.value})} 
      />
      <input 
        type="number" 
        placeholder="Your Budget ($)" 
        onChange={(e) => setFormData({...formData, budget: parseInt(e.target.value)})} 
      />
      <button type="submit">Post to Edumerce</button>
    </form>
  );
};
