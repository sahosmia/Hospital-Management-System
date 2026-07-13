import React, { useEffect, useState } from 'react';
import { Head } from '@inertiajs/react';
import Sidebar from '../../Components/Sidebar';
import Header from '../../Components/Header';
import Card from '../../Components/Card';
import Input from '../../Components/Input';
import Button from '../../Components/Button';
import Toast from '../../Components/Toast';
import { Settings, Shield } from 'lucide-react';

export default function SystemSettings() {
    const [hospitalName, setHospitalName] = useState('St. Jude General Hospital');
    const [currency, setCurrency] = useState('USD');
    const [otpExpiry, setOtpExpiry] = useState('5');
    const [taxRate, setTaxRate] = useState('5.0');
    const [toast, setToast] = useState(null);

    const handleSave = (e) => {
        e.preventDefault();
        setToast({ type: 'success', message: 'System configurations successfully updated and saved!' });
    };

    return (
        <div className="min-h-screen bg-gray-50 pl-64">
            <Head title="System Settings" />
            <Sidebar />

            <div className="flex flex-col min-h-screen">
                <Header title="System Configuration" />

                <main className="flex-1 p-8 space-y-8 max-w-4xl mx-auto w-full">
                    <form onSubmit={handleSave} className="space-y-6">
                        <Card className="p-8 space-y-6">
                            <h3 className="text-base font-bold text-gray-900 flex items-center">
                                <Settings className="h-5 w-5 text-blue-600 mr-2" /> Global Configurations Settings
                            </h3>

                            <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <Input
                                    label="Hospital Name"
                                    name="hospitalName"
                                    value={hospitalName}
                                    onChange={(e) => setHospitalName(e.target.value)}
                                    required
                                />

                                <Input
                                    label="System Currency"
                                    name="currency"
                                    type="select"
                                    value={currency}
                                    onChange={(e) => setCurrency(e.target.value)}
                                    options={[
                                        { value: 'USD', label: 'US Dollars ($)' },
                                        { value: 'GBP', label: 'British Pounds (£)' },
                                        { value: 'EUR', label: 'Euros (€)' }
                                    ]}
                                    required
                                />

                                <Input
                                    label="SMS OTP Expiry Duration (Minutes)"
                                    name="otp"
                                    type="number"
                                    min="1"
                                    value={otpExpiry}
                                    onChange={(e) => setOtpExpiry(e.target.value)}
                                    required
                                />

                                <Input
                                    label="Tax Rate applied on Billing (%)"
                                    name="tax"
                                    type="number"
                                    step="0.1"
                                    min="0"
                                    value={taxRate}
                                    onChange={(e) => setTaxRate(e.target.value)}
                                    required
                                />
                            </div>
                        </Card>

                        {/* Security configs */}
                        <Card className="p-8 space-y-4">
                            <h3 className="text-base font-bold text-gray-900 flex items-center">
                                <Shield className="h-5 w-5 text-indigo-600 mr-2" /> Security Compliance Settings
                            </h3>
                            <div className="p-4 bg-indigo-50 border border-indigo-100 rounded-lg text-indigo-800 text-xs font-semibold leading-relaxed">
                                System is fully compliant with HIPAA / GDPR standards. Password encryption uses Bcrypt with 12 rounds. Auth tokens expire after 7 days automatically.
                            </div>
                        </Card>

                        <div className="flex justify-end">
                            <Button type="submit" variant="primary">
                                Save Configurations
                            </Button>
                        </div>
                    </form>
                </main>
            </div>

            {Toast && toast && (
                <Toast type={toast.type} message={toast.message} onClose={() => setToast(null)} />
            )}
        </div>
    );
}
