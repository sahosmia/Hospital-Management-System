import React, { useEffect, useState } from 'react';
import { Head } from '@inertiajs/react';
import Sidebar from '../../Components/Sidebar';
import Header from '../../Components/Header';
import Card from '../../Components/Card';
import Badge from '../../Components/Badge';
import Button from '../../Components/Button';
import Modal from '../../Components/Modal';
import Input from '../../Components/Input';
import Toast from '../../Components/Toast';
import { Bed, UserPlus, LogOut, CheckCircle2 } from 'lucide-react';

export default function BedManagement() {
    const [beds, setBeds] = useState([]);
    const [stats, setStats] = useState({ total: 8, occupied: 3, available: 5 });
    const [selectedBed, setSelectedBed] = useState(null);
    const [allocateModalOpen, setAllocateModalOpen] = useState(false);
    const [patientId, setPatientId] = useState('');
    const [toast, setToast] = useState(null);

    useEffect(() => {
        setBeds([
            { id: 1, bed_number: 'G-101', bed_type: 'General', ward_name: 'General Ward', status: 'available', daily_charge: 50.00 },
            { id: 2, bed_number: 'G-102', bed_type: 'General', ward_name: 'General Ward', status: 'available', daily_charge: 50.00 },
            { id: 3, bed_number: 'P-201', bed_type: 'Private', ward_name: 'Private Ward', status: 'occupied', daily_charge: 150.00, current_patient: { name: 'Ada Lovelace' } },
            { id: 4, bed_number: 'P-202', bed_type: 'Private', ward_name: 'Private Ward', status: 'available', daily_charge: 150.00 },
            { id: 5, bed_number: 'D-301', bed_type: 'Deluxe', ward_name: 'Deluxe Ward', status: 'available', daily_charge: 300.00 },
            { id: 6, bed_number: 'ICU-401', bed_type: 'ICU', ward_name: 'ICU Ward', status: 'occupied', daily_charge: 800.00, current_patient: { name: 'Alan Turing' } },
            { id: 7, bed_number: 'ICU-402', bed_type: 'ICU', ward_name: 'ICU Ward', status: 'available', daily_charge: 800.00 },
            { id: 8, bed_number: 'HDU-501', bed_type: 'HDU', ward_name: 'HDU Ward', status: 'occupied', daily_charge: 500.00, current_patient: { name: 'Margaret Hamilton' } },
        ]);
    }, []);

    const openAllocateModal = (bed) => {
        setSelectedBed(bed);
        setPatientId('');
        setAllocateModalOpen(true);
    };

    const handleAllocate = () => {
        if (!patientId) {
            setToast({ type: 'error', message: 'Patient selection is mandatory.' });
            return;
        }

        setBeds(beds.map(b => b.id === selectedBed.id ? { ...b, status: 'occupied', current_patient: { name: patientId } } : b));
        setToast({ type: 'success', message: `Bed ${selectedBed.bed_number} allocated successfully!` });
        setAllocateModalOpen(false);
    };

    const handleRelease = (bed) => {
        setBeds(beds.map(b => b.id === bed.id ? { ...b, status: 'available', current_patient: null } : b));
        setToast({ type: 'success', message: `Bed ${bed.bed_number} released and marked available.` });
    };

    return (
        <div className="min-h-screen bg-gray-50 pl-64">
            <Head title="Bed & Admission Allocation" />
            <Sidebar />

            <div className="flex flex-col min-h-screen">
                <Header title="Ward Bed Directory" />

                <main className="flex-1 p-8 space-y-8 max-w-7xl mx-auto w-full">
                    {/* Upper occupancy summary cards */}
                    <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <Card className="p-6 text-center">
                            <h4 className="text-sm font-semibold text-gray-400 uppercase">Total Ward Beds</h4>
                            <p className="text-3xl font-extrabold text-slate-800 mt-2">{stats.total}</p>
                        </Card>
                        <Card className="p-6 text-center">
                            <h4 className="text-sm font-semibold text-gray-400 uppercase">Occupied Beds</h4>
                            <p className="text-3xl font-extrabold text-rose-600 mt-2">{beds.filter(b => b.status === 'occupied').length}</p>
                        </Card>
                        <Card className="p-6 text-center">
                            <h4 className="text-sm font-semibold text-gray-400 uppercase">Available Beds</h4>
                            <p className="text-3xl font-extrabold text-emerald-600 mt-2">{beds.filter(b => b.status === 'available').length}</p>
                        </Card>
                    </div>

                    {/* Beds Grid */}
                    <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                        {beds.map((bed) => {
                            const isOccupied = bed.status === 'occupied';
                            return (
                                <Card key={bed.id} className={`p-6 flex flex-col justify-between border-t-4 ${isOccupied ? 'border-rose-500' : 'border-emerald-500'}`}>
                                    <div className="space-y-3">
                                        <div className="flex justify-between items-center">
                                            <span className="font-extrabold text-gray-900 text-lg">{bed.bed_number}</span>
                                            <Badge type={isOccupied ? 'error' : 'success'}>
                                                {bed.status}
                                            </Badge>
                                        </div>
                                        <p className="text-xs text-gray-500 font-semibold">{bed.bed_type} &bull; {bed.ward_name}</p>

                                        {isOccupied ? (
                                            <p className="text-xs text-gray-900 font-medium">Patient: <span className="font-bold text-gray-800">{bed.current_patient?.name}</span></p>
                                        ) : (
                                            <p className="text-xs text-gray-400">Vacuum-cleaned & Disinfected</p>
                                        )}
                                    </div>

                                    <div className="border-t border-gray-100 mt-4 pt-3 flex justify-between items-center text-xs">
                                        <span className="font-bold text-blue-600">${bed.daily_charge.toFixed(2)}/day</span>
                                        {isOccupied ? (
                                            <Button size="sm" variant="danger" onClick={() => handleRelease(bed)}>
                                                <LogOut className="h-3 w-3 mr-1" /> Release
                                            </Button>
                                        ) : (
                                            <Button size="sm" variant="success" onClick={() => openAllocateModal(bed)}>
                                                <UserPlus className="h-3 w-3 mr-1" /> Allocate
                                            </Button>
                                        )}
                                    </div>
                                </Card>
                            );
                        })}
                    </div>
                </main>
            </div>

            {/* Allocate Bed Modal */}
            <Modal
                isOpen={allocateModalOpen}
                title={`Allocate Bed: ${selectedBed?.bed_number}`}
                onClose={() => setAllocateModalOpen(false)}
                onConfirm={handleAllocate}
                confirmText="Confirm Bed Allocation"
                confirmVariant="success"
            >
                <div className="space-y-4">
                    <Input
                        label="Select Patient Profile"
                        name="patient"
                        type="select"
                        value={patientId}
                        onChange={(e) => setPatientId(e.target.value)}
                        placeholder="Choose inpatient..."
                        options={[
                            { value: 'Ada Lovelace', label: 'Ada Lovelace (PAT-0001)' },
                            { value: 'Alan Turing', label: 'Alan Turing (PAT-0002)' },
                            { value: 'Grace Hopper', label: 'Grace Hamilton (PAT-0005)' }
                        ]}
                    />
                </div>
            </Modal>

            {toast && (
                <Toast type={toast.type} message={toast.message} onClose={() => setToast(null)} />
            )}
        </div>
    );
}
