import React, { useEffect, useState } from 'react';
import { Head, Link } from '@inertiajs/react';
import Sidebar from '../../Components/Sidebar';
import Header from '../../Components/Header';
import StatsCard from '../../Components/StatsCard';
import Card from '../../Components/Card';
import Badge from '../../Components/Badge';
import { Users, Calendar, Bed, Wallet, AlertTriangle, Activity } from 'lucide-react';

export default function AdminDashboard() {
    const [stats, setStats] = useState({
        patients: 154,
        appointments: 28,
        occupiedBeds: 3,
        totalBeds: 8,
        dailyRevenue: 1250.00,
    });

    const [alerts, setAlerts] = useState([]);
    const [recentAdmissions, setRecentAdmissions] = useState([]);

    useEffect(() => {
        setAlerts([
            { id: 1, type: 'inventory', message: 'Foley Catheter stock is low (9 remaining)' },
            { id: 2, type: 'system', message: 'Backup has not been run in the last 24 hours' }
        ]);

        setRecentAdmissions([
            { id: 1, name: 'Ada Lovelace', type: 'planned', bed: 'P-201', time: '10:00 AM', status: 'active' },
            { id: 2, name: 'Alan Turing', type: 'emergency', bed: 'ICU-401', time: '12:30 PM', status: 'active' }
        ]);
    }, []);

    return (
        <div className="min-h-screen bg-gray-50 pl-64">
            <Head title="Admin Dashboard" />
            <Sidebar />

            <div className="flex flex-col min-h-screen">
                <Header title="Hospital Command Center" />

                <main className="flex-1 p-8 space-y-8 max-w-7xl mx-auto w-full">
                    {/* Stats Widget Rows */}
                    <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                        <StatsCard icon={Users} number={stats.patients} label="Total Registrations" trend="+15 this week" />
                        <StatsCard icon={Calendar} number={stats.appointments} label="Today's Bookings" trend="8 completed" />
                        <StatsCard icon={Bed} number={`${stats.occupiedBeds}/${stats.totalBeds}`} label="Ward Occupancy" trend="37.5% capacity" />
                        <StatsCard icon={Wallet} number={`$${stats.dailyRevenue.toFixed(2)}`} label="Daily Revenue" trend="+12.4% over avg" trendType="success" />
                    </div>

                    {/* Alerts center */}
                    {alerts.length > 0 && (
                        <div className="space-y-4">
                            <h3 className="text-base font-bold text-gray-900 flex items-center">
                                <AlertTriangle className="h-5 w-5 text-amber-500 mr-2" /> Critical Operational Warnings
                            </h3>
                            <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                                {alerts.map((alert) => (
                                    <div key={alert.id} className="p-4 bg-amber-50 border border-amber-200 text-amber-800 text-sm rounded-lg flex items-center font-medium">
                                        <AlertTriangle className="h-5 w-5 text-amber-500 mr-3 flex-shrink-0" />
                                        {alert.message}
                                    </div>
                                ))}
                            </div>
                        </div>
                    )}

                    {/* Split details panel */}
                    <div className="grid grid-cols-1 lg:grid-cols-3 gap-8">
                        {/* Recent admissions */}
                        <div className="lg:col-span-2 space-y-6">
                            <div className="flex justify-between items-center">
                                <h3 className="text-lg font-bold text-gray-900">Recent Admitted Inpatients</h3>
                                <Link href="/admin/beds" className="text-sm font-semibold text-blue-600 hover:text-blue-500">
                                    Manage Bed Assignments
                                </Link>
                            </div>
                            <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                                {recentAdmissions.map((adm) => (
                                    <Card key={adm.id} className="p-5 flex flex-col justify-between hover:shadow-xs transition-shadow">
                                        <div>
                                            <div className="flex justify-between items-center">
                                                <h4 className="font-bold text-gray-900">{adm.name}</h4>
                                                <Badge type={adm.type === 'emergency' ? 'error' : 'info'}>{adm.type}</Badge>
                                            </div>
                                            <p className="text-xs text-gray-500 mt-2">Bed Allocation: <span className="font-semibold text-gray-800">{adm.bed}</span></p>
                                            <p className="text-xs text-gray-500 mt-1">Admit Time: {adm.time}</p>
                                        </div>
                                        <div className="mt-4 border-t border-gray-100 pt-3 flex justify-between items-center text-xs">
                                            <span className="text-gray-400">Status: Active Inpatient</span>
                                            <Badge type="success">Active</Badge>
                                        </div>
                                    </Card>
                                ))}
                            </div>
                        </div>

                        {/* Quick Action Hub */}
                        <div className="space-y-6">
                            <h3 className="text-lg font-bold text-gray-900">Hospital Administration Hub</h3>
                            <Card className="p-6 space-y-4">
                                <Link href="/admin/users" className="block">
                                    <button className="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold text-sm py-2.5 px-4 rounded-md transition-all text-center">
                                        Staff Directory Control
                                    </button>
                                </Link>
                                <Link href="/admin/surgeries" className="block">
                                    <button className="w-full bg-slate-800 hover:bg-slate-900 text-white font-semibold text-sm py-2.5 px-4 rounded-md transition-all text-center">
                                        Book Surgical OT
                                    </button>
                                </Link>
                                <Link href="/admin/billing" className="block">
                                    <button className="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-semibold text-sm py-2.5 px-4 rounded-md transition-all text-center">
                                        Aggregate Patient Billing
                                    </button>
                                </Link>
                            </Card>
                        </div>
                    </div>
                </main>
            </div>
        </div>
    );
}
