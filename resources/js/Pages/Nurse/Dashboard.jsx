import React, { useEffect, useState } from 'react';
import { Head, Link } from '@inertiajs/react';
import Sidebar from '../../Components/Sidebar';
import Header from '../../Components/Header';
import StatsCard from '../../Components/StatsCard';
import Card from '../../Components/Card';
import Badge from '../../Components/Badge';
import Table from '../../Components/Table';
import Button from '../../Components/Button';
import Modal from '../../Components/Modal';
import Input from '../../Components/Input';
import Toast from '../../Components/Toast';
import { Bed, Pill, CheckCircle, AlertTriangle } from 'lucide-react';

export default function NurseDashboard() {
    const [bedStats, setBedStats] = useState({ total: 8, occupied: 3, available: 5 });
    const [activeAdmissions, setActiveAdmissions] = useState([]);
    const [todayMedications, setTodayMedications] = useState([]);
    const [selectedMed, setSelectedMed] = useState(null);
    const [administerModalOpen, setAdministerModalOpen] = useState(false);
    const [dosage, setDosage] = useState('');
    const [notes, setNotes] = useState('');
    const [status, setStatus] = useState('given');
    const [toast, setToast] = useState(null);

    useEffect(() => {
        // Load mock active patients
        setActiveAdmissions([
            { id: 1, patient: { name: 'Ada Lovelace', patient_id: 'PAT-0001' }, bed: { bed_number: 'P-201' }, doctor: { name: 'Dr. Elizabeth Blackwell' } },
            { id: 2, patient: { name: 'Alan Turing', patient_id: 'PAT-0002' }, bed: { bed_number: 'ICU-401' }, doctor: { name: 'Dr. Robert Koch' } }
        ]);

        // Load today's active scheduled medications
        setTodayMedications([
            { id: 10, admission_id: 1, admission: { patient: { name: 'Ada Lovelace' } }, medicine_name: 'Atorvastatin', dosage: '20mg', frequency: 'Once daily', scheduled_time: '20:00', status: 'active' },
            { id: 11, admission_id: 2, admission: { patient: { name: 'Alan Turing' } }, medicine_name: 'Aspirin', dosage: '75mg', frequency: 'Once daily', scheduled_time: '08:00', status: 'active' }
        ]);
    }, []);

    const openAdministerModal = (med) => {
        setSelectedMed(med);
        setDosage(med.dosage);
        setNotes('');
        setStatus('given');
        setAdministerModalOpen(true);
    };

    const handleAdministerSubmit = () => {
        if (!dosage) {
            setToast({ type: 'error', message: 'Dosage details are required.' });
            return;
        }

        // Simulate administration submission
        setToast({ type: 'success', message: `Medication ${selectedMed.medicine_name} recorded as ${status}!` });
        setTodayMedications(todayMedications.filter(m => m.id !== selectedMed.id));
        setAdministerModalOpen(false);
    };

    return (
        <div className="min-h-screen bg-gray-50 pl-64">
            <Head title="Nurse Dashboard" />
            <Sidebar />

            <div className="flex flex-col min-h-screen">
                <Header title="Nurse Administration Console" />

                <main className="flex-1 p-8 space-y-8 max-w-7xl mx-auto w-full">
                    {/* Metrics Grid */}
                    <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <StatsCard icon={Bed} number={`${bedStats.occupied}/${bedStats.total}`} label="Bed Occupancy" trend="37.5% occupied" />
                        <StatsCard icon={Pill} number={todayMedications.length} label="Pending Administrations" trend="Time-sensitive" trendType="danger" />
                        <StatsCard icon={CheckCircle} number={activeAdmissions.length} label="Active Inpatients" />
                    </div>

                    {/* Split content */}
                    <div className="grid grid-cols-1 lg:grid-cols-3 gap-8">
                        {/* Today's Medications */}
                        <div className="lg:col-span-2 space-y-6">
                            <h3 className="text-lg font-bold text-gray-900">Today's Scheduled Medication Rounds</h3>
                            {todayMedications.length === 0 ? (
                                <Card className="p-6 text-center text-gray-500 text-sm">
                                    All scheduled medication rounds completed! Excellent work.
                                </Card>
                            ) : (
                                <Table headers={['Time', 'Patient', 'Medicine', 'Dosage', 'Frequency', 'Action']}>
                                    {todayMedications.map((med) => (
                                        <tr key={med.id} className="hover:bg-gray-50">
                                            <td className="px-6 py-4 font-semibold text-gray-900">{med.scheduled_time}</td>
                                            <td className="px-6 py-4 font-semibold text-gray-900">{med.admission.patient.name}</td>
                                            <td className="px-6 py-4">{med.medicine_name}</td>
                                            <td className="px-6 py-4 font-medium">{med.dosage}</td>
                                            <td className="px-6 py-4 text-xs text-gray-500">{med.frequency}</td>
                                            <td className="px-6 py-4">
                                                <Button size="sm" variant="success" onClick={() => openAdministerModal(med)}>
                                                    Record Admin
                                                </Button>
                                            </td>
                                        </tr>
                                    ))}
                                </Table>
                            )}
                        </div>

                        {/* Active Inpatients list */}
                        <div className="space-y-6">
                            <h3 className="text-lg font-bold text-gray-900">Ward Occupancy Directory</h3>
                            <div className="space-y-4">
                                {activeAdmissions.map((adm) => (
                                    <Card key={adm.id} className="p-5 flex items-center justify-between hover:shadow-xs transition-shadow">
                                        <div>
                                            <h4 className="font-semibold text-gray-900 text-sm">{adm.patient.name}</h4>
                                            <p className="text-xs text-gray-500 mt-1">{adm.patient.patient_id} &bull; Bed: {adm.bed.bed_number}</p>
                                        </div>
                                        <Badge type="info">Inpatient</Badge>
                                    </Card>
                                ))}
                            </div>
                        </div>
                    </div>
                </main>
            </div>

            {/* Record Administration Modal */}
            <Modal
                isOpen={administerModalOpen}
                title={`Record Administration: ${selectedMed?.medicine_name}`}
                onClose={() => setAdministerModalOpen(false)}
                onConfirm={handleAdministerSubmit}
                confirmText="Save Administration"
                confirmVariant="success"
            >
                <div className="space-y-4">
                    <Input
                        label="Status"
                        name="status"
                        type="select"
                        value={status}
                        onChange={(e) => setStatus(e.target.value)}
                        options={[
                            { value: 'given', label: 'Given' },
                            { value: 'missed', label: 'Missed' },
                            { value: 'refused', label: 'Patient Refused' },
                            { value: 'held', label: 'Held on Doctor Order' },
                            { value: 'delayed', label: 'Delayed' },
                        ]}
                    />

                    <Input
                        label="Dosage Given"
                        name="dosage"
                        value={dosage}
                        onChange={(e) => setDosage(e.target.value)}
                        required
                    />

                    <Input
                        label="Clinical Notes / Side Effects"
                        name="notes"
                        type="textarea"
                        placeholder="Add vitals before/after, notes on patient response, etc."
                        value={notes}
                        onChange={(e) => setNotes(e.target.value)}
                    />
                </div>
            </Modal>

            {toast && (
                <Toast
                    type={toast.type}
                    message={toast.message}
                    onClose={() => setToast(null)}
                />
            )}
        </div>
    );
}
