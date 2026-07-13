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
import { Users, Plus, ShieldAlert } from 'lucide-react';

export default function UserManagement() {
    const [users, setUsers] = useState([]);
    const [modalOpen, setModalOpen] = useState(false);
    const [name, setName] = useState('');
    const [email, setEmail] = useState('');
    const [phone, setPhone] = useState('');
    const [role, setRole] = useState('doctor');
    const [password, setPassword] = useState('');
    const [toast, setToast] = useState(null);

    useEffect(() => {
        setUsers([
            { id: 1, name: 'Super Admin', email: 'admin@hms.com', phone: '1234567890', role: 'super_admin' },
            { id: 2, name: 'Hospital Admin', email: 'hospital@hms.com', phone: '1234567891', role: 'hospital_admin' },
            { id: 3, name: 'Dr. Elizabeth Blackwell', email: 'doctor1@hms.com', phone: '1112223331', role: 'doctor' },
            { id: 4, name: 'Florence Nightingale', email: 'nurse1@hms.com', phone: '2223334440', role: 'nurse' }
        ]);
    }, []);

    const handleCreateUser = () => {
        if (!name || !email || !password) {
            setToast({ type: 'error', message: 'Name, Email, and Password are required fields.' });
            return;
        }

        const newUser = {
            id: users.length + 1,
            name,
            email,
            phone,
            role
        };

        setUsers([...users, newUser]);
        setToast({ type: 'success', message: 'Staff user profile successfully created!' });
        setModalOpen(false);

        // Reset
        setName('');
        setEmail('');
        setPhone('');
        setPassword('');
    };

    return (
        <div className="min-h-screen bg-gray-50 pl-64">
            <Head title="Staff Directory Control" />
            <Sidebar />

            <div className="flex flex-col min-h-screen">
                <Header title="Staff Directory Control" />

                <main className="flex-1 p-8 space-y-8 max-w-7xl mx-auto w-full">
                    <div className="flex justify-between items-center bg-white p-6 rounded-lg border border-gray-200 shadow-sm">
                        <h3 className="text-lg font-bold text-gray-900 flex items-center">
                            <Users className="h-5 w-5 text-blue-600 mr-2" /> Hospital Staff Users
                        </h3>
                        <Button variant="primary" onClick={() => setModalOpen(true)}>
                            <Plus className="h-5 w-5 mr-2" /> Add Staff Profile
                        </Button>
                    </div>

                    <Card>
                        <Table headers={['Staff Name', 'Email address', 'Phone Contact', 'Role Permission', 'Status']}>
                            {users.map((user) => (
                                <tr key={user.id} className="hover:bg-gray-50">
                                    <td className="px-6 py-4 font-bold text-gray-900">{user.name}</td>
                                    <td className="px-6 py-4">{user.email}</td>
                                    <td className="px-6 py-4 text-sm text-gray-500">{user.phone || 'N/A'}</td>
                                    <td className="px-6 py-4">
                                        <Badge type="info" className="capitalize">
                                            {user.role.replace('_', ' ')}
                                        </Badge>
                                    </td>
                                    <td className="px-6 py-4">
                                        <Badge type="success">Active</Badge>
                                    </td>
                                </tr>
                            ))}
                        </Table>
                    </Card>
                </main>
            </div>

            {/* Create Staff Modal */}
            <Modal
                isOpen={modalOpen}
                title="Create Staff Account"
                onClose={() => setModalOpen(false)}
                onConfirm={handleCreateUser}
                confirmText="Create Profile"
            >
                <div className="space-y-4">
                    <Input label="Staff Full Name" name="name" value={name} onChange={(e) => setName(e.target.value)} required />
                    <Input label="Email Address" name="email" type="email" value={email} onChange={(e) => setEmail(e.target.value)} required />
                    <Input label="Phone Contact" name="phone" value={phone} onChange={(e) => setPhone(e.target.value)} />
                    <Input
                        label="Assign Role Profile"
                        name="role"
                        type="select"
                        value={role}
                        onChange={(e) => setRole(e.target.value)}
                        options={[
                            { value: 'super_admin', label: 'Super Admin' },
                            { value: 'hospital_admin', label: 'Hospital Admin' },
                            { value: 'doctor', label: 'Consulting Doctor' },
                            { value: 'nurse', label: 'Ward Nurse' },
                            { value: 'receptionist', label: 'Front-desk Receptionist' },
                            { value: 'cashier', label: 'Cashier Finance officer' }
                        ]}
                    />
                    <Input label="Temporary Security Password" name="password" type="password" value={password} onChange={(e) => setPassword(e.target.value)} required />
                </div>
            </Modal>

            {toast && (
                <Toast type={toast.type} message={toast.message} onClose={() => setToast(null)} />
            )}
        </div>
    );
}
