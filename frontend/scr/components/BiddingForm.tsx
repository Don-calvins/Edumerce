import React, { useState } from 'react';
import { apiRequest } from '../api/client';

interface BiddingFormProps {
  assignmentId: number;
}

const BiddingForm: React.FC<BiddingFormProps> = ({ assignmentId }) => {
  const [amount, setAmount] = useState<number>(0);
  const [proposal, setProposal] = useState<string>('');

  const submitBid = async () => {
    try {
      await apiRequest('/bids', 'POST', {
        assignment_id: assignmentId,
        amount: amount,
        proposal: proposal
      });
      alert("Bid submitted successfully!");
    } catch (err: any) {
      alert(err.message);
    }
  };

  return (
    <div className="p-6 bg-gray-50 border rounded-lg">
      <h3 className="text-xl font-bold mb-4">Place your Quote</h3>
      <label className="block mb-2">Your Bid Amount ($)</label>
      <input 
        type="number" 
        className="w-full p-2 mb-4 border"
        onChange={(e) => setAmount(Number(e.target.value))}
      />
      <label className="block mb-2">Proposal Details</label>
      <textarea 
        className="w-full p-2 mb-4 border"
        placeholder="Why should the student pick you?"
        onChange={(e) => setProposal(e.target.value)}
      />
      <button 
        onClick={submitBid}
        className="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700"
      >
        Submit Bid
      </button>
    </div>
  );
};

export default BiddingForm;
