import React, { useEffect, useState } from 'react';
import { Head } from '@inertiajs/react';
import Sidebar from '../../Components/Sidebar';
import Header from '../../Components/Header';
import Card from '../../Components/Card';
import Badge from '../../Components/Badge';
import Table from '../../Components/Table';
import Button from '../../Components/Button';
import Modal from '../../Components/Modal';
import Input from '../../Components/Input';
import Toast from '../../Components/Toast';
import { Wallet, Landmark, CreditCard, ShieldCheck } from 'lucide-react';

export default function PatientBills() {
    const [bills, setBills] = useState([]);
    const [selectedBill, setSelectedDoctorBill] = useState(null);
    const [payModalOpen, setPayModalOpen] = useState(false);
    const [paymentMethod, setPaymentMethod] = useState('card');
    const [cardNumber, setCardNumber] = useState('');
    const [cvv, setCvv] = useState('');
    const [toast, setToast] = useState(null);

    useEffect(() => {
        setBills([
            { id: 101, category: 'Consultation Fee', doctor: 'Dr. Elizabeth Blackwell', amount: 150.00, tax: 7.50, net: 157.50, date: '2026-07-13', status: 'pending' },
            { id: 98, category: 'General Ward Admission Charge', doctor: 'Dr. Robert Koch', amount: 200.00, tax: 10.00, net: 210.00, date: '2026-06-25', status: 'completed' }
        ]);
    }, []);

    const openPayModal = (bill) => {
        setSelectedDoctorBill(bill);
        setCardNumber('');
        setCvv('');
        setPayModalOpen(true);
    };

    const handlePaymentSubmit = (e) => {
        e.preventDefault();
        setBills(bills.map(b => b.id === selectedBill.id ? { ...b, status: 'completed' } : b));
        setToast({ type: 'success', message: 'Payment successfully processed! Receipt downloaded.' });
        setPayModalOpen(false);
    };

    return (
        <div className="min-h-screen bg-gray-50 pl-64">
            <Head title="My Invoices & Payments" />
            <Sidebar />

            <div className="flex flex-col min-h-screen">
                <Header title="My Invoices & Payments" />

                <main className="flex-1 p-8 space-y-8 max-w-7xl mx-auto w-full">
                    {/* Bill directory list */}
                    <Card>
                        <Table headers={['Invoice ID', 'Bill Item', 'Consulting Physician', 'Issued Date', 'Total Amount', 'Status', 'Action']}>
                            {bills.map((bill) => (
                                <tr key={bill.id} className="hover:bg-gray-50">
                                    <td className="px-6 py-4 font-semibold text-gray-900">#INV-{bill.id}</td>
                                    <td className="px-6 py-4 font-medium text-gray-900">{bill.category}</td>
                                    <td className="px-6 py-4 text-gray-500">{bill.doctor}</td>
                                    <td className="px-6 py-4 text-sm text-gray-600">{bill.date}</td>
                                    <td className="px-6 py-4 font-bold text-gray-900">${bill.net.toFixed(2)}</td>
                                    <td className="px-6 py-4">
                                        <Badge type={bill.status === 'pending' ? 'error' : 'success'} className="capitalize">
                                            {bill.status}
                                        </Badge>
                                    </td>
                                    <td className="px-6 py-4">
                                        {bill.status === 'pending' ? (
                                            <Button size="sm" variant="danger" onClick={() => openPayModal(bill)}>
                                                Pay Online
                                            </Button>
                                        ) : (
                                            <Button size="sm" variant="outline" onClick={() => setToast({ type: 'info', message: 'Simulated invoice download successfully started.' })}>
                                                Receipt
                                            </Button>
                                        )}
                                    </td>
                                </tr>
                            ))}
                        </Table>
                    </Card>
                </main>
            </div>

            {/* Pay Modal */}
            <Modal
                isOpen={payModalOpen}
                title={`Secure Checkout: Invoice #INV-${selectedBill?.id}`}
                onClose={() => setPayModalOpen(false)}
                onConfirm={handlePaymentSubmit}
                confirmText={`Pay $${selectedBill?.net.toFixed(2)}`}
                confirmVariant="danger"
            >
                <form className="space-y-4">
                    <div className="bg-gray-50 p-4 rounded-lg flex justify-between items-center text-sm border border-gray-100 mb-4">
                        <span className="text-gray-500 font-semibold">{selectedBill?.category}</span>
                        <span className="font-bold text-blue-600">${selectedBill?.net.toFixed(2)}</span>
                    </div>

                    <Input
                        label="Payment Method"
                        name="method"
                        type="select"
                        value={paymentMethod}
                        onChange={(e) => setPaymentMethod(e.target.value)}
                        options={[
                            { value: 'card', label: 'Secured Credit / Debit Card' },
                            { value: 'mobile_banking', label: 'Mobile Banking Checkout' },
                            { value: 'insurance', label: 'Insurance Policy Claims' },
                        ]}
                    />

                    {paymentMethod === 'card' && (
                        <div className="grid grid-cols-3 gap-4">
                            <Input
                                label="Card Number"
                                name="cardNumber"
                                placeholder="1111 2222 3333 4444"
                                className="col-span-2"
                                value={cardNumber}
                                onChange={(e) => setCardNumber(e.target.value)}
                                required
                            />
                            <Input
                                label="CVV"
                                name="cvv"
                                type="password"
                                placeholder="•••"
                                value={cvv}
                                onChange={(e) => setCvv(e.target.value)}
                                required
                            />
                        </div>
                    )}
                </form>
            </Modal>

            {toast && (
                <Toast type={toast.type} message={toast.message} onClose={() => setToast(null)} />
            )}
        </div>
    );
}
