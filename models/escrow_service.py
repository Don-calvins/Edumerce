from models import Contract, EscrowStatus # Importing from your models.py
from sqlalchemy.orm import Session

class EscrowService:
    def __init__(self, db: Session):
        self.db = db

    def initiate_contract(self, assignment_id, tutor_id, student_id, amount):
        """Creates the contract record after a bid is accepted."""
        new_contract = Contract(
            assignment_id=assignment_id,
            tutor_id=tutor_id,
            student_id=student_id,
            total_amount=amount,
            status='awaiting_deposit'
        )
        self.db.add(new_contract)
        self.db.commit()
        return new_contract

    def process_first_milestone(self, contract_id):
        """Handles the first 50% payment."""
        contract = self.db.query(Contract).filter(Contract.id == contract_id).first()
        
        # Calculate 50%
        deposit = contract.total_amount * 0.5
        
        # Logic: Call Stripe/Payment API here
        # if payment_success:
        contract.amount_held_escrow = deposit
        contract.milestone_1_paid = True
        contract.status = 'active'
        
        self.db.commit()
        return {"message": f"First milestone of ${deposit} secured. Tutor can start."}

    def process_final_release(self, contract_id):
        """Handles the final 50% payment and releases everything to tutor."""
        contract = self.db.query(Contract).filter(Contract.id == contract_id).first()
        
        if not contract.milestone_1_paid:
            return {"error": "First milestone must be paid first."}

        # Calculate final 50%
        final_payment = contract.total_amount * 0.5
        
        # Logic: Call Stripe/Payment API for the second half
        # if payment_success:
        contract.amount_held_escrow += final_payment
        contract.milestone_2_paid = True
        
        # Release funds logic (Minus platform fee)
        platform_fee = contract.total_amount * 0.10 # 10% example fee
        payout_amount = contract.total_amount - platform_fee
        
        # trigger_payout_to_tutor(contract.tutor_id, payout_amount)
        
        contract.status = 'completed'
        self.db.commit()
        return {"message": "Project completed. Funds released to tutor."}
