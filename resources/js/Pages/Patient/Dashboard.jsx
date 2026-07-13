import React, { useEffect, useState } from 'react';
import { Head, Link } from '@inertiajs/react';
import Sidebar from '../../Components/Sidebar';
import Header from '../../Components/Header';
import StatsCard from '../../Components/StatsCard';
import Card from '../../Components/Card';
import Badge from '../../Components/Badge';
import { Calendar, ClipboardList, Wallet, Sparkles, PlusCircle } from 'lucide-react';

export default function PatientDashboard() {
    const [stats, setStats] = useState({ appointments: 0, activeAdmissions: 0, outstandingBills: 0 });
    const [upcomingAppointments, setUpcomingAppointments] = useState([]);
    const [recentBills, setRecentBills] = useState([]);

    useEffect(() => {
        // Simulating data loading
        setStats({ appointments: 2, activeAdmissions: 0, outstandingBills: 1 });
        setUpcomingAppointments([
            { id: 1, doctor: { name: 'Dr. Elizabeth Blackwell' }, date: '2026-07-20', time: '10:15 AM', status: 'scheduled', type: 'physical' },
            { id: 2, doctor: { name: 'Dr. Robert Koch' }, date: '2026-07-28', time: '11:00 AM', status: 'scheduled', type: 'video' }
        ]);
        setRecentBills([
            { id: 101, category: 'Consultation Fee', amount: 150.00, date: '2026-07-13', status: 'pending' }
        ]);
    }, []);

    return (
        <div className="min-h-screen bg-gray-50 pl-64">
            <Head title="Patient Dashboard" />
            <Sidebar />

            <div className="flex flex-col min-h-screen">
                <Header title="Patient Dashboard" />

                <main className="flex-1 p-8 space-y-8 max-w-7xl mx-auto w-full">
                    {/* Welcome card */}
                    <div className="bg-gradient-to-r from-blue-600 to-indigo-600 rounded-lg p-6 text-white shadow-xs flex items-center justify-between">
                        <div className="max-w-md">
                            <h2 className="text-2xl font-bold flex items-center">
                                Welcome back! <Sparkles className="h-6 w-6 text-yellow-300 ml-2" />
                            </h2>
                            <p className="mt-2 text-blue-100 text-sm leading-relaxed">
                                You can manage your appointments, view historical reports, check medication lists, and pay bills online securely.
                            </p>
                        </div>
                        <Link href="/patient/book">
                            <button className="bg-white text-blue-600 hover:bg-blue-50 px-5 py-2.5 rounded-md font-semibold text-sm transition-all shadow-xs flex items-center">
                                <PlusCircle className="h-5 w-5 mr-2" /> Book New Appointment
                            </button>
                        </Link>
                    </div>

                    {/* Stats Grid */}
                    <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <StatsCard icon={Calendar} number={stats.appointments} label="Total Bookings" />
                        <StatsCard icon={ClipboardList} number={stats.activeAdmissions} label="Hospital Admissions" />
                        <StatsCard icon={Wallet} number={stats.outstandingBills} label="Outstanding Invoices" trend="1 Pending" trendType="danger" />
                    </div>

                    {/* Main Content split */}
                    <div className="grid grid-cols-1 lg:grid-cols-3 gap-8">
                        {/* Upcoming Bookings */}
                        <div className="lg:col-span-2 space-y-6">
                            <h3 className="text-lg font-bold text-gray-900">Upcoming Consultations</h3>
                            {upcomingAppointments.length === 0 ? (
                                <Card className="p-6 text-center text-gray-500 text-sm">
                                    No scheduled appointments found. Click 'Book New Appointment' above to schedule one.
                                </Card>
                            ) : (
                                <div className="space-y-4">
                                    {upcomingAppointments.map((apt) => (
                                        <Card key={apt.id} className="p-6 flex items-center justify-between hover:shadow-xs transition-shadow">
                                            <div className="flex items-center space-x-4">
                                                <div className="h-12 w-12 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center font-bold">
                                                    {apt.doctor.name.replace('Dr. ', '').substring(0, 1)}
                                                </div>
                                                <div>
                                                    <h4 className="font-semibold text-gray-900">{apt.doctor.name}</h4>
                                                    <p className="text-sm text-gray-500 mt-0.5">{apt.date} at {apt.time}</p>
                                                    <Badge type="info" className="mt-2 capitalize">{apt.type}</Badge>
                                                </div>
                                            </div>
                                            <Badge type="success">{apt.status}</Badge>
                                        </Card>
                                    ))}
                                </div>
                            )}
                        </div>

                        {/* Recent Bill Quickpay */}
                        <div className="space-y-6">
                            <h3 className="text-lg font-bold text-gray-900">Quick Pay Invoices</h3>
                            {recentBills.length === 0 ? (
                                <Card className="p-6 text-center text-gray-500 text-sm">
                                    All clear! No outstanding balances.
                                </Card>
                            ) : (
                                <div className="space-y-4">
                                    {recentBills.map((bill) => (
                                        <Card key={bill.id} className="p-5 border-l-4 border-rose-500">
                                            <div className="flex justify-between items-start">
                                                <div>
                                                    <h4 className="font-semibold text-gray-900 text-sm">{bill.category}</h4>
                                                    <p className="text-xs text-gray-500 mt-1">Issued on {bill.date}</p>
                                                </div>
                                                <span className="font-bold text-rose-600">${bill.amount.toFixed(2)}</span>
                                            </div>
                                            <Link href="/patient/bills">
                                                <button className="w-full mt-4 bg-rose-600 hover:bg-rose-700 text-white text-xs font-semibold py-2 rounded transition-colors">
                                                    Proceed to Secured Payment
                                                </button>
                                            </Link>
                                        </Card>
                                    ))}
                                </div>
                            )}
                        </div>
                    </div>
                </main>
            </div>
        </div>
    );
}
