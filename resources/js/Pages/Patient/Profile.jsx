import React, { useEffect, useState } from 'react';
import { Head } from '@inertiajs/react';
import Sidebar from '../../Components/Sidebar';
import Header from '../../Components/Header';
import Input from '../../Components/Input';
import Button from '../../Components/Button';
import Card from '../../Components/Card';
import Toast from '../../Components/Toast';
import { User, ShieldAlert, PhoneCall } from 'lucide-react';

export default function PatientProfile() {
    const [name, setName] = useState('Ada Lovelace');
    const [email, setEmail] = useState('patient1@hms.com');
    const [phone, setPhone] = useState('5556667771');
    const [dob, setDob] = useState('1995-12-10');
    const [bloodGroup, setBloodGroup] = useState('A-');
    const [address, setAddress] = useState('Baker Street 221B, London');
    const [emergencyName, setEmergencyName] = useState('Charles Babbage');
    const [emergencyPhone, setEmergencyPhone] = useState('9998887771');
    const [toast, setToast] = useState(null);

    const handleSave = (e) => {
        e.preventDefault();
        setToast({ type: 'success', message: 'Profile and medical details successfully synchronized!' });
    };

    return (
        <div className="min-h-screen bg-gray-50 pl-64">
            <Head title="My Health Profile" />
            <Sidebar />

            <div className="flex flex-col min-h-screen">
                <Header title="My Health Profile" />

                <main className="flex-1 p-8 space-y-8 max-w-5xl mx-auto w-full">
                    <form onSubmit={handleSave} className="grid grid-cols-1 lg:grid-cols-3 gap-8">
                        {/* Profile left sidebar summary */}
                        <Card className="p-6 text-center space-y-4">
                            <div className="h-24 w-24 rounded-full bg-blue-100 text-blue-700 flex items-center justify-center font-bold text-3xl mx-auto uppercase">
                                AD
                            </div>
                            <div>
                                <h3 className="text-lg font-bold text-gray-900">{name}</h3>
                                <p className="text-sm text-gray-500 capitalize">Patient Account &bull; PAT-0001</p>
                            </div>
                            <div className="pt-4 border-t border-gray-100 grid grid-cols-2 gap-4 text-sm">
                                <div className="p-3 bg-gray-50 rounded-lg">
                                    <span className="block text-xs text-gray-500 font-medium">Blood Group</span>
                                    <span className="font-bold text-blue-600 mt-1 block">{bloodGroup}</span>
                                </div>
                                <div className="p-3 bg-gray-50 rounded-lg">
                                    <span className="block text-xs text-gray-500 font-medium">Age</span>
                                    <span className="font-bold text-blue-600 mt-1 block">30 Yrs</span>
                                </div>
                            </div>
                        </Card>

                        {/* Editable profile fields */}
                        <div className="lg:col-span-2 space-y-8">
                            {/* Personal settings */}
                            <Card className="p-6 space-y-4">
                                <h3 className="text-md font-bold text-gray-900 flex items-center">
                                    <User className="h-5 w-5 text-blue-600 mr-2" /> Demographics & Identity Info
                                </h3>
                                <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <Input label="Full Name" name="name" value={name} onChange={(e) => setName(e.target.value)} required />
                                    <Input label="Date of Birth" name="dob" type="date" value={dob} onChange={(e) => setDob(e.target.value)} required />
                                    <Input label="Email Address" name="email" type="email" value={email} onChange={(e) => setEmail(e.target.value)} required />
                                    <Input label="Primary Phone" name="phone" value={phone} onChange={(e) => setPhone(e.target.value)} required />
                                </div>
                                <Input label="Residential Address" name="address" type="textarea" value={address} onChange={(e) => setAddress(e.target.value)} />
                            </Card>

                            {/* Emergency & medical alerts */}
                            <Card className="p-6 space-y-4">
                                <h3 className="text-md font-bold text-gray-900 flex items-center">
                                    <PhoneCall className="h-5 w-5 text-indigo-600 mr-2" /> Emergency Contact Contacts
                                </h3>
                                <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <Input label="Contact Person Name" name="emergencyName" value={emergencyName} onChange={(e) => setEmergencyName(e.target.value)} />
                                    <Input label="Contact Person Phone" name="emergencyPhone" value={emergencyPhone} onChange={(e) => setEmergencyPhone(e.target.value)} />
                                </div>
                            </Card>

                            {/* Medical alerts */}
                            <Card className="p-6 space-y-4">
                                <h3 className="text-md font-bold text-gray-900 flex items-center">
                                    <ShieldAlert className="h-5 w-5 text-rose-600 mr-2" /> Known Medical Allergies & Alerts
                                </h3>
                                <div className="p-4 bg-rose-50 border border-rose-100 rounded-lg text-rose-800 text-sm font-medium flex items-center">
                                    <ShieldAlert className="h-5 w-5 text-rose-600 mr-3 flex-shrink-0" />
                                    Alert: Hypersensitive to Penicillin. Double check prescriptions.
                                </div>
                            </Card>

                            <div className="flex justify-end">
                                <Button type="submit" variant="primary">
                                    Save Changes & Sync Profiles
                                </Button>
                            </div>
                        </div>
                    </form>
                </main>
            </div>

            {toast && (
                <Toast type={toast.type} message={toast.message} onClose={() => setToast(null)} />
            )}
        </div>
    );
}
