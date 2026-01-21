export interface Assignment {
  id: number;
  title: string;
  description: string;
  budget: number;
  status: 'open' | 'in_progress' | 'completed' | 'disputed';
}

export interface Bid {
  id: number;
  assignment_id: number;
  tutor_id: number;
  amount: number;
  proposal: string;
  status: 'pending' | 'accepted' | 'rejected';
}
