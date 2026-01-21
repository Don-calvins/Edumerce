from sqlalchemy import Column, Integer, String, Float, ForeignKey, DateTime, Boolean, Enum, Text
from sqlalchemy.ext.declarative import declarative_base
from sqlalchemy.orm import relationship
import datetime

Base = declarative_base()

# --- PHASE 1: USER AUTH & ROLES ---
class User(Base):
    __tablename__ = 'users'
    id = Column(Integer, primary_key=True)
    email = Column(String(255), unique=True, nullable=False)
    password_hash = Column(String(255), nullable=False)
    role = Column(Enum('student', 'tutor', 'admin', name='user_roles'), nullable=False)
    is_verified = Column(Boolean, default=False) # Phase 2: Tutor verification
    stripe_account_id = Column(String(255), nullable=True) # For Escrow payments

# --- PHASE 1: ASSIGNMENTS & BIDDING ---
class Assignment(Base):
    __tablename__ = 'assignments'
    id = Column(Integer, primary_key=True)
    student_id = Column(Integer, ForeignKey('users.id'))
    title = Column(String(255), nullable=False)
    description = Column(Text, nullable=False)
    deadline = Column(DateTime, nullable=False)
    status = Column(Enum('open', 'in_progress', 'completed', 'disputed', name='job_status'), default='open')
    
    bids = relationship("Bid", back_populates="assignment")

class Bid(Base):
    __tablename__ = 'bids'
    id = Column(Integer, primary_key=True)
    assignment_id = Column(Integer, ForeignKey('assignments.id'))
    tutor_id = Column(Integer, ForeignKey('users.id'))
    amount = Column(Float, nullable=False)
    proposal = Column(Text)
    status = Column(Enum('pending', 'accepted', 'rejected', name='bid_status'), default='pending')
    
    assignment = relationship("Assignment", back_populates="bids")

# --- PHASE 1: 50/50 ESCROW LOGIC ---
class Contract(Base):
    __tablename__ = 'contracts'
    id = Column(Integer, primary_key=True)
    assignment_id = Column(Integer, ForeignKey('assignments.id'))
    tutor_id = Column(Integer, ForeignKey('users.id'))
    student_id = Column(Integer, ForeignKey('users.id'))
    total_amount = Column(Float, nullable=False)
    amount_held_escrow = Column(Float, default=0.0)
    
    # 50/50 Payment Tracking
    milestone_1_paid = Column(Boolean, default=False) # First 50%
    milestone_2_paid = Column(Boolean, default=False) # Final 50%
    
    file_url = Column(String(500)) # Link to uploaded work
    status = Column(Enum('awaiting_deposit', 'active', 'submitted', 'completed', 'disputed', name='contract_status'))

# --- PHASE 1 & 2: CHAT SYSTEM ---
class Conversation(Base):
    __tablename__ = 'conversations'
    id = Column(Integer, primary_key=True)
    assignment_id = Column(Integer, ForeignKey('assignments.id'))
    student_id = Column(Integer, ForeignKey('users.id'))
    tutor_id = Column(Integer, ForeignKey('users.id'))

class Message(Base):
    __tablename__ = 'messages'
    id = Column(Integer, primary_key=True)
    conversation_id = Column(Integer, ForeignKey('conversations.id'))
    sender_id = Column(Integer, ForeignKey('users.id'))
    content = Column(Text, nullable=False)
    created_at = Column(DateTime, default=datetime.datetime.utcnow)
    is_read = Column(Boolean, default=False)

# --- PHASE 2: DISPUTES ---
class Dispute(Base):
    __tablename__ = 'disputes'
    id = Column(Integer, primary_key=True)
    contract_id = Column(Integer, ForeignKey('contracts.id'))
    raised_by = Column(Integer, ForeignKey('users.id'))
    reason = Column(Text, nullable=False)
    admin_notes = Column(Text)
    status = Column(Enum('pending', 'resolved', 'refunded', name='dispute_status'), default='pending')
