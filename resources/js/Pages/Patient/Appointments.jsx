import React, { useEffect, useState } from 'react';
import { Head, Link } from '@inertiajs/react';
import Sidebar from '../../Components/Sidebar';
import Header from '../../Components/Header';
import Card from '../../Components/Card';
import Badge from '../../Components/Badge';
import Table from '../../Components/Table';
import Button from '../../Components/Button';
import Toast from '../../Components/Toast';
import { Calendar, User, Clock, ArrowRight } from 'lucide-react';

export default function PatientAppointments() {
    const [appointments, setAppointments] = useState([]);
    const [toast, setToast] = useState(null);

    useEffect(() => {
        setAppointments([
            { id: 1, doctor: { name: 'Dr. Elizabeth Blackwell', specialty: 'Cardiology' }, date: '2026-07-20', time: '10:15 AM', status: 'scheduled', type: 'physical' },
            { id: 2, doctor: { name: 'Dr. Robert Koch', specialty: 'Neurology' }, date: '2026-07-28', time: '11:00 AM', status: 'scheduled', type: 'video' },
            { id: 3, doctor: { name: 'Dr. Virginia Apgar', specialty: 'Pediatrics' }, date: '2026-06-05', time: '09:30 AM', status: 'completed', type: 'telephone' }
        ]);
    }, []);

    const handleCancel = (id) => {
        setAppointments(appointments.map(apt => apt.id === id ? { ...apt, status: 'cancelled' } : apt));
        setToast({ type: 'success', message: 'Consultation successfully cancelled!' });
    };

    return (
        <div className="min-h-screen bg-gray-50 pl-64">
            <Head title="My Appointments" />
            <Sidebar />

            <div className="flex flex-col min-h-screen">
                <Header title="My Appointments" />

                <main className="flex-1 p-8 space-y-8 max-w-7xl mx-auto w-full">
                    <div className="flex justify-between items-center">
                        <h3 className="text-lg font-bold text-gray-900">Consultation Bookings</h3>
                        <Link href="/patient/book">
                            <Button variant="primary">
                                Book New Appointment
                            </Button>
                        </Link>
                    </div>

                    {/* Table View */}
                    <Card>
                        <Table headers={['Doctor / Specialization', 'Appointment Date', 'Time Slot', 'Type', 'Status', 'Action']}>
                            {appointments.map((apt) => (
                                <tr key={apt.id} className="hover:bg-gray-50">
                                    <td className="px-6 py-4">
                                        <div className="flex items-center space-x-3">
                                            <div className="h-8 w-8 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center font-bold text-xs uppercase">
                                                {apt.doctor.name.replace('Dr. ', '').substring(0, 1)}
                                            </div>
                                            <div>
                                                <h4 className="font-semibold text-gray-900 text-sm">{apt.doctor.name}</h4>
                                                <p className="text-xs text-gray-400 capitalize">{apt.doctor.specialty}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td className="px-6 py-4 text-sm font-semibold text-gray-900">{apt.date}</td>
                                    <td className="px-6 py-4 text-sm text-gray-600">{apt.time}</td>
                                    <td className="px-6 py-4">
                                        <Badge type="info" className="capitalize">{apt.type}</Badge>
                                    </td>
                                    <td className="px-6 py-4">
                                        <Badge type={apt.status === 'scheduled' ? 'success' : apt.status === 'completed' ? 'gray' : 'error'} className="capitalize">
                                            {apt.status}
                                        </Badge>
                                    </td>
                                    <td className="px-6 py-4">
                                        {apt.status === 'scheduled' && (
                                            <Button size="sm" variant="danger" onClick={() => handleCancel(apt.id)}>
                                                Cancel
                                            </Button>
                                        )}
                                    </td>
                                </tr>
                            ))}
                        </Table>
                    </Card>
                </main>
            </div>

            {toast && (
                <Toast type={toast.type} message={toast.message} onClose={() => setToast(null)} />
            )}
        </div>
    );
}
