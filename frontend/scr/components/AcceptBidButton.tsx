import React, { useState } from 'react';

const AcceptBidButton = ({ bidId, amount }) => {
  const [loading, setLoading] = useState(false);

  const handleAccept = async () => {
    setLoading(true);
    
    // 1. Tell the Backend to create the Escrow Contract
    const response = await fetch('/api/escrow/initiate', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ bidId: bidId })
    });

    const data = await response.json();

    if (data.success) {
      alert(`Success! Please pay the first installment of $${amount / 2}`);
      // 2. Redirect to Stripe Payment Page
      window.location.href = data.stripeUrl;
    }
    setLoading(false);
  };

  return (
    <button onClick={handleAccept} disabled={loading}>
      {loading ? "Processing..." : "Accept Quote & Pay 50%"}
    </button>
  );
};

export default AcceptBidButton;
