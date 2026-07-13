import React, { useEffect, useState } from 'react';
import { Head } from '@inertiajs/react';
import Sidebar from '../../Components/Sidebar';
import Header from '../../Components/Header';
import Card from '../../Components/Card';
import Table from '../../Components/Table';
import Badge from '../../Components/Badge';
import Button from '../../Components/Button';
import Modal from '../../Components/Modal';
import Input from '../../Components/Input';
import Toast from '../../Components/Toast';
import { Wallet, Landmark, Receipt, FileText } from 'lucide-react';

export default function AdminBilling() {
    const [admissions, setAdmissions] = useState([]);
    const [selectedAdm, setSelectedAdm] = useState(null);
    const [billDetails, setBillDetails] = useState(null);
    const [invoiceModalOpen, setInvoiceModalOpen] = useState(false);
    const [checkoutModalOpen, setCheckoutModalOpen] = useState(false);
    const [amountPaid, setAmountPaid] = useState('');
    const [method, setMethod] = useState('cash');
    const [toast, setToast] = useState(null);

    useEffect(() => {
        setAdmissions([
            { id: 1, sNum: 'ADM-20260713-101', name: 'Ada Lovelace', patient_id: 'PAT-0001', bed: 'P-201', admitted: '2026-07-13', status: 'active', daily_charge: 150.00 },
            { id: 2, sNum: 'ADM-20260713-102', name: 'Alan Turing', patient_id: 'PAT-0002', bed: 'ICU-401', admitted: '2026-07-13', status: 'active', daily_charge: 800.00 }
        ]);
    }, []);

    const handleGenerateInvoice = (adm) => {
        setSelectedAdm(adm);
        
        // Simulating billing calculation details
        const bedCharge = adm.daily_charge * 1; // 1 day
        const medCharge = 45.00;
        const subtotal = bedCharge + medCharge;
        const tax = Math.round(subtotal * 0.05, 2);
        const total = subtotal + tax;

        setBillDetails({
            bed_charge: bedCharge,
            med_charge: medCharge,
            subtotal,
            tax,
            total
        });
        setInvoiceModalOpen(true);
    };

    const handleCheckoutSubmit = () => {
        setToast({ type: 'success', message: 'Secured checkout completed! Receipt printed.' });
        setCheckoutModalOpen(false);
    };

    return (
        <div className="min-h-screen bg-gray-50 pl-64">
            <Head title="Billing Operations" />
            <Sidebar />

            <div className="flex flex-col min-h-screen">
                <Header title="Billing & Cashier Terminal" />

                <main className="flex-1 p-8 space-y-8 max-w-7xl mx-auto w-full">
                    {/* Active Inpatients admissions ready to generate bills */}
                    <Card className="p-6 space-y-4">
                        <h3 className="text-base font-bold text-gray-900 flex items-center">
                            <Receipt className="h-5 w-5 text-emerald-600 mr-2" /> Active Admissions Invoice Aggregator
                        </h3>
                        <Table headers={['Admission ID', 'Patient Name', 'Bed Allocated', 'Admitted Date', 'Daily Rate', 'Pending Balance', 'Action']}>
                            {admissions.map((adm) => (
                                <tr key={adm.id} className="hover:bg-gray-50">
                                    <td className="px-6 py-4 font-semibold text-gray-900">{adm.sNum}</td>
                                    <td className="px-6 py-4 font-bold text-gray-900">{adm.name}</td>
                                    <td className="px-6 py-4">{adm.bed}</td>
                                    <td className="px-6 py-4 text-xs text-gray-500">{adm.admitted}</td>
                                    <td className="px-6 py-4 font-semibold">${adm.daily_charge.toFixed(2)}</td>
                                    <td className="px-6 py-4 font-bold text-gray-900">${(adm.daily_charge * 1 + 45.00).toFixed(2)}</td>
                                    <td className="px-6 py-4 flex space-x-2">
                                        <Button size="sm" variant="success" onClick={() => handleGenerateInvoice(adm)}>
                                            Generate Statement
                                        </Button>
                                        <Button size="sm" variant="outline" onClick={() => {
                                            setSelectedAdm(adm);
                                            setAmountPaid((adm.daily_charge * 1 + 45.00).toFixed(2));
                                            setCheckoutModalOpen(true);
                                        }}>
                                            Checkout
                                        </Button>
                                    </td>
                                </tr>
                            ))}
                        </Table>
                    </Card>
                </main>
            </div>

            {/* Bill Statement aggregate Modal */}
            <Modal
                isOpen={invoiceModalOpen}
                title={`Itemized Medical Statement: ${selectedAdm?.name}`}
                onClose={() => setInvoiceModalOpen(false)}
                onConfirm={() => {
                    setInvoiceModalOpen(false);
                    setAmountPaid(billDetails?.total.toFixed(2));
                    setCheckoutModalOpen(true);
                }}
                confirmText="Proceed to Payment"
                confirmVariant="success"
            >
                {billDetails && (
                    <div className="space-y-4 text-sm text-gray-600">
                        <div className="border-b border-gray-100 pb-3 flex justify-between">
                            <span className="font-semibold text-gray-800">Bed Ward Charges ({selectedAdm?.bed})</span>
                            <span>${billDetails.bed_charge.toFixed(2)}</span>
                        </div>
                        <div className="border-b border-gray-100 pb-3 flex justify-between">
                            <span className="font-semibold text-gray-800">Medication Administrations</span>
                            <span>${billDetails.med_charge.toFixed(2)}</span>
                        </div>
                        <div className="border-b border-gray-100 pb-3 flex justify-between font-medium">
                            <span>Subtotal</span>
                            <span>${billDetails.subtotal.toFixed(2)}</span>
                        </div>
                        <div className="border-b border-gray-100 pb-3 flex justify-between">
                            <span>Tax (5.0%)</span>
                            <span>${billDetails.tax.toFixed(2)}</span>
                        </div>
                        <div className="flex justify-between items-center text-base font-extrabold text-gray-900 border-t border-gray-200 pt-3">
                            <span>Total Balance Due</span>
                            <span className="text-blue-600">${billDetails.total.toFixed(2)}</span>
                        </div>
                    </div>
                )}
            </Modal>

            {/* Cashier Checkout Modal */}
            <Modal
                isOpen={checkoutModalOpen}
                title={`Cashier Payment Terminal: ${selectedAdm?.name}`}
                onClose={() => setCheckoutModalOpen(false)}
                onConfirm={handleCheckoutSubmit}
                confirmText="Fulfill Checkout"
                confirmVariant="success"
            >
                <div className="space-y-4">
                    <Input
                        label="Payment Method"
                        name="method"
                        type="select"
                        value={method}
                        onChange={(e) => setMethod(e.target.value)}
                        options={[
                            { value: 'cash', label: 'Cash Payment' },
                            { value: 'card', label: 'Secured Card Checkout' },
                            { value: 'mobile', label: 'Mobile Banking Checkout' },
                            { value: 'insurance', label: 'Insurance Policy Claims' }
                        ]}
                    />

                    <Input
                        label="Amount Fulfilling ($)"
                        name="pay"
                        type="number"
                        value={amountPaid}
                        onChange={(e) => setAmountPaid(e.target.value)}
                        required
                    />
                </div>
            </Modal>

            {toast && (
                <Toast type={toast.type} message={toast.message} onClose={() => setToast(null)} />
            )}
        </div>
    );
}
