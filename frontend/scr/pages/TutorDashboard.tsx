import React from 'react';
import { Assignment } from '../types'; // Importing logic from a .ts file

const TutorDashboard = () => {
  // Logic: Imagining we fetched this from the Python backend
  const activeJobs: Assignment[] = [
    { id: 1, title: "Python Scripting", budget: 200, status: 'in_progress', description: "..." }
  ];

  return (
    <div className="p-8">
      <h1 className="text-2xl font-bold">My Active Gigs</h1>
      {activeJobs.map(job => (
        <div key={job.id} className="border-b p-4 flex justify-between">
          <div>
            <p className="font-semibold">{job.title}</p>
            <p className="text-sm text-gray-500">Budget: ${job.budget}</p>
          </div>
          {/* Action button to upload Phase 1 files */}
          <button className="bg-green-600 text-white px-3 py-1 rounded">
            Upload Deliverable
          </button>
        </div>
      ))}
    </div>
  );
};

export default TutorDashboard;
