import React, { useEffect, useState } from 'react';
import { Head } from '@inertiajs/react';
import Sidebar from '../../Components/Sidebar';
import Header from '../../Components/Header';
import Card from '../../Components/Card';
import Table from '../../Components/Table';
import Badge from '../../Components/Badge';
import Input from '../../Components/Input';
import Button from '../../Components/Button';
import Modal from '../../Components/Modal';
import Toast from '../../Components/Toast';
import { Users, Search, PlusCircle } from 'lucide-react';

export default function PatientManagement() {
    const [patients, setPatients] = useState([]);
    const [search, setSearch] = useState('');
    const [modalOpen, setModalOpen] = useState(false);
    const [name, setName] = useState('');
    const [phone, setPhone] = useState('');
    const [email, setEmail] = useState('');
    const [bloodGroup, setBloodGroup] = useState('O+');
    const [toast, setToast] = useState(null);

    useEffect(() => {
        setPatients([
            { id: 1, name: 'Ada Lovelace', patient_id: 'PAT-0001', phone: '5556667771', blood_group: 'A-', registered: '2026-07-13' },
            { id: 2, name: 'Alan Turing', patient_id: 'PAT-0002', phone: '5556667772', blood_group: 'O+', registered: '2026-07-13' }
        ]);
    }, []);

    const filteredPatients = patients.filter(pat => 
        pat.name.toLowerCase().includes(search.toLowerCase()) || 
        pat.patient_id.includes(search)
    );

    const handleCreatePatient = () => {
        if (!name || !phone) {
            setToast({ type: 'error', message: 'Name and Phone are mandatory fields.' });
            return;
        }

        const newPat = {
            id: patients.length + 1,
            name,
            patient_id: 'PAT-' + Math.floor(Math.random() * (9999 - 1000 + 1) + 1000),
            phone,
            blood_group: bloodGroup,
            registered: new Date().toISOString().split('T')[0]
        };

        setPatients([...patients, newPat]);
        setToast({ type: 'success', message: 'Patient profile successfully registered!' });
        setModalOpen(false);
    };

    return (
        <div className="min-h-screen bg-gray-50 pl-64">
            <Head title="Patient Directory" />
            <Sidebar />

            <div className="flex flex-col min-h-screen">
                <Header title="Patient Directory" />

                <main className="flex-1 p-8 space-y-8 max-w-7xl mx-auto w-full">
                    {/* Upper row */}
                    <div className="flex justify-between items-center bg-white p-6 rounded-lg border border-gray-200 shadow-sm">
                        <div className="w-full max-w-md">
                            <Input
                                placeholder="Search by name, phone, or patient ID..."
                                name="search"
                                value={search}
                                onChange={(e) => setSearch(e.target.value)}
                                className="mb-0"
                            />
                        </div>
                        <Button variant="primary" onClick={() => setModalOpen(true)}>
                            <PlusCircle className="h-5 w-5 mr-2" /> Register Patient (Offline)
                        </Button>
                    </div>

                    {/* Patients table */}
                    <Card>
                        <Table headers={['Patient ID', 'Full Name', 'Contact Phone', 'Blood Group', 'Date Registered', 'Status']}>
                            {filteredPatients.map((pat) => (
                                <tr key={pat.id} className="hover:bg-gray-50">
                                    <td className="px-6 py-4 font-semibold text-blue-600">{pat.patient_id}</td>
                                    <td className="px-6 py-4 font-bold text-gray-900">{pat.name}</td>
                                    <td className="px-6 py-4">{pat.phone}</td>
                                    <td className="px-6 py-4 font-semibold text-gray-700">{pat.blood_group}</td>
                                    <td className="px-6 py-4 text-sm text-gray-500">{pat.registered}</td>
                                    <td className="px-6 py-4">
                                        <Badge type="success">Active</Badge>
                                    </td>
                                </tr>
                            ))}
                        </Table>
                    </Card>
                </main>
            </div>

            {/* Offline registration modal */}
            <Modal
                isOpen={modalOpen}
                title="Register Patient (Receptionist Entry)"
                onClose={() => setModalOpen(false)}
                onConfirm={handleCreatePatient}
                confirmText="Register Profile"
            >
                <div className="space-y-4">
                    <Input label="Patient Full Name" name="name" value={name} onChange={(e) => setName(e.target.value)} required />
                    <Input label="Primary Phone Contact" name="phone" value={phone} onChange={(e) => setPhone(e.target.value)} required />
                    <Input label="Email address (Optional)" name="email" value={email} onChange={(e) => setEmail(e.target.value)} />
                    <Input
                        label="Blood Group"
                        name="blood"
                        type="select"
                        value={bloodGroup}
                        onChange={(e) => setBloodGroup(e.target.value)}
                        options={[
                            { value: 'A+', label: 'A+' },
                            { value: 'A-', label: 'A-' },
                            { value: 'B+', label: 'B+' },
                            { value: 'B-', label: 'B-' },
                            { value: 'O+', label: 'O+' },
                            { value: 'O-', label: 'O-' },
                            { value: 'AB+', label: 'AB+' },
                            { value: 'AB-', label: 'AB-' }
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
