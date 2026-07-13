import React, { useEffect, useState } from 'react';
import { Head } from '@inertiajs/react';
import Sidebar from '../../Components/Sidebar';
import Header from '../../Components/Header';
import Card from '../../Components/Card';
import Input from '../../Components/Input';
import Button from '../../Components/Button';
import Toast from '../../Components/Toast';
import { Activity, Clock } from 'lucide-react';

export default function SurgeryScheduler() {
    const [surgeries, setSurgeries] = useState([]);
    const [selectedOt, setSelectedOt] = useState('OT-01');
    const [patient, setPatient] = useState('');
    const [surgeon, setSurgeon] = useState('');
    const [anesthesiologist, setAnesthesiologist] = useState('');
    const [date, setDate] = useState('');
    const [time, setTime] = useState('');
    const [surgeryType, setSurgeryType] = useState('Bypass Surgery');
    const [toast, setToast] = useState(null);

    useEffect(() => {
        setSurgeries([
            { id: 1, sNum: 'SRG-001', name: 'Open Heart Surgery', patient: 'Alan Turing', surgeon: 'Dr. Elizabeth Blackwell', ot: 'Cardiac Specialist OT', date: '2026-07-15', status: 'scheduled' }
        ]);
    }, []);

    const handleSchedule = (e) => {
        e.preventDefault();
        if (!patient || !surgeon || !date || !time) {
            setToast({ type: 'error', message: 'Please complete all required fields.' });
            return;
        }

        const newSurg = {
            id: surgeries.length + 1,
            sNum: 'SRG-' + Math.floor(Math.random() * (999 - 100 + 1) + 100),
            name: surgeryType,
            patient,
            surgeon,
            ot: selectedOt === 'OT-01' ? 'General Operating Theater 1' : 'Cardiac Specialist OT',
            date,
            status: 'scheduled'
        };

        setSurgeries([...surgeries, newSurg]);
        setToast({ type: 'success', message: 'Surgery scheduled and OT reserved successfully!' });

        // Reset form
        setPatient('');
        setSurgeon('');
        setDate('');
        setTime('');
    };

    return (
        <div className="min-h-screen bg-gray-50 pl-64">
            <Head title="Surgery room & OT Scheduler" />
            <Sidebar />

            <div className="flex flex-col min-h-screen">
                <Header title="Operating Theater (OT) Scheduler" />

                <main className="flex-1 p-8 space-y-8 max-w-7xl mx-auto w-full">
                    <div className="grid grid-cols-1 lg:grid-cols-3 gap-8">
                        {/* Booking form */}
                        <Card className="p-6 h-fit space-y-4">
                            <h3 className="text-base font-bold text-gray-900 flex items-center">
                                <Activity className="h-5 w-5 text-blue-600 mr-2" /> Book Operating Theater Room
                            </h3>

                            <form onSubmit={handleSchedule} className="space-y-4">
                                <Input
                                    label="Select Operating Theater"
                                    name="ot"
                                    type="select"
                                    value={selectedOt}
                                    onChange={(e) => setSelectedOt(e.target.value)}
                                    options={[
                                        { value: 'OT-01', label: 'OT-01 - General Operating Theater 1' },
                                        { value: 'OT-02', label: 'OT-02 - Cardiac Specialist OT' },
                                        { value: 'OT-03', label: 'OT-03 - Neuro & Trauma OT' }
                                    ]}
                                />

                                <Input
                                    label="Patient Name"
                                    name="patient"
                                    placeholder="e.g. Alan Turing"
                                    value={patient}
                                    onChange={(e) => setPatient(e.target.value)}
                                    required
                                />

                                <Input
                                    label="Lead Surgeon"
                                    name="surgeon"
                                    type="select"
                                    value={surgeon}
                                    onChange={(e) => setSurgeon(e.target.value)}
                                    placeholder="Select surgeon..."
                                    options={[
                                        { value: 'Dr. Elizabeth Blackwell', label: 'Dr. Elizabeth Blackwell (Cardiology)' },
                                        { value: 'Dr. Robert Koch', label: 'Dr. Robert Koch (Neurology)' },
                                        { value: 'Dr. Virginia Apgar', label: 'Dr. Virginia Apgar (Pediatrics)' }
                                    ]}
                                    required
                                />

                                <Input
                                    label="Anesthesiologist (Optional)"
                                    name="anesthesiologist"
                                    placeholder="e.g. Dr. Virginia Apgar"
                                    value={anesthesiologist}
                                    onChange={(e) => setAnesthesiologist(e.target.value)}
                                />

                                <div className="grid grid-cols-2 gap-4">
                                    <Input label="Scheduled Date" name="date" type="date" value={date} onChange={(e) => setDate(e.target.value)} required />
                                    <Input label="Scheduled Time" name="time" type="time" value={time} onChange={(e) => setTime(e.target.value)} required />
                                </div>

                                <Input label="Operation Description" name="surgeryType" value={surgeryType} onChange={(e) => setSurgeryType(e.target.value)} required />

                                <Button type="submit" variant="primary" className="w-full">
                                    Schedule & Lock OT
                                </Button>
                            </form>
                        </Card>

                        {/* Scheduled surgeries list */}
                        <div className="lg:col-span-2 space-y-6">
                            <h3 className="text-lg font-bold text-gray-900">Today's & Upcoming Surgical List</h3>

                            <div className="space-y-4">
                                {surgeries.map((surg) => (
                                    <Card key={surg.id} className="p-6 border-l-4 border-blue-500">
                                        <div className="flex justify-between items-start">
                                            <div>
                                                <Badge type="info" className="mb-2 uppercase text-xs">{surg.sNum}</Badge>
                                                <h4 className="font-bold text-gray-900 text-base">{surg.name}</h4>
                                                <p className="text-xs text-gray-500 mt-2">Patient: <span className="font-semibold text-gray-800">{surg.patient}</span> &bull; Lead: <span className="font-semibold text-gray-800">{surg.surgeon}</span></p>
                                                <p className="text-xs text-gray-400 mt-1">Location: {surg.ot} &bull; Date: {surg.date}</p>
                                            </div>
                                            <Badge type="success">{surg.status}</Badge>
                                        </div>
                                    </Card>
                                ))}
                            </div>
                        </div>
                    </div>
                </main>
            </div>

            {toast && (
                <Toast type={toast.type} message={toast.message} onClose={() => setToast(null)} />
            )}
        </div>
    );
}
