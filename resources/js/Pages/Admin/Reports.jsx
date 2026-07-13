import React, { useEffect, useState } from 'react';
import { Head } from '@inertiajs/react';
import Sidebar from '../../Components/Sidebar';
import Header from '../../Components/Header';
import Card from '../../Components/Card';
import Badge from '../../Components/Badge';
import Table from '../../Components/Table';
import Button from '../../Components/Button';
import Toast from '../../Components/Toast';
import { FileText, TrendingUp, Calendar, Bed } from 'lucide-react';

export default function Reports() {
    const [reports, setReports] = useState([]);
    const [toast, setToast] = useState(null);

    useEffect(() => {
        setReports([
            { id: 1, type: 'daily', date: '2026-07-13', bookings: 28, admissions: 2, surgeries: 1, revenue: 1250.00 },
            { id: 2, type: 'daily', date: '2026-07-12', bookings: 24, admissions: 1, surgeries: 2, revenue: 1980.00 },
            { id: 3, type: 'daily', date: '2026-07-11', bookings: 31, admissions: 3, surgeries: 0, revenue: 950.00 }
        ]);
    }, []);

    const handleExport = (rep) => {
        setToast({ type: 'success', message: `Report exported successfully as hms_report_daily_${rep.date}.csv!` });
    };

    return (
        <div className="min-h-screen bg-gray-50 pl-64">
            <Head title="Reports & Financial Analytics" />
            <Sidebar />

            <div className="flex flex-col min-h-screen">
                <Header title="Reports & Operational Analytics" />

                <main className="flex-1 p-8 space-y-8 max-w-7xl mx-auto w-full">
                    {/* Metrics Chart Placeholders */}
                    <div className="grid grid-cols-1 md:grid-cols-2 gap-8">
                        {/* Occupancy trends chart */}
                        <Card className="p-6 space-y-4">
                            <h4 className="text-sm font-bold text-gray-500 uppercase flex items-center">
                                <Bed className="h-5 w-5 text-blue-500 mr-2" /> Bed Occupancy Rate (Last 7 Days)
                            </h4>
                            <div className="h-48 bg-gray-50 rounded-lg flex items-end justify-between p-6 border border-gray-100">
                                <div className="w-8 bg-blue-400 rounded-t-xs h-[30%] text-center text-[10px] text-white">30%</div>
                                <div className="w-8 bg-blue-400 rounded-t-xs h-[35%] text-center text-[10px] text-white">35%</div>
                                <div className="w-8 bg-blue-500 rounded-t-xs h-[50%] text-center text-[10px] text-white">50%</div>
                                <div className="w-8 bg-blue-500 rounded-t-xs h-[45%] text-center text-[10px] text-white">45%</div>
                                <div className="w-8 bg-blue-600 rounded-t-xs h-[60%] text-center text-[10px] text-white">60%</div>
                                <div className="w-8 bg-blue-600 rounded-t-xs h-[55%] text-center text-[10px] text-white">55%</div>
                                <div className="w-8 bg-blue-700 rounded-t-xs h-[37.5%] text-center text-[10px] text-white">37.5%</div>
                            </div>
                        </Card>

                        {/* Revenue Trends */}
                        <Card className="p-6 space-y-4">
                            <h4 className="text-sm font-bold text-gray-500 uppercase flex items-center">
                                <TrendingUp className="h-5 w-5 text-emerald-500 mr-2" /> Daily Revenue Collection
                            </h4>
                            <div className="h-48 bg-gray-50 rounded-lg flex items-end justify-between p-6 border border-gray-100">
                                <div className="w-8 bg-emerald-400 rounded-t-xs h-[40%] text-center text-[10px] text-white">$950</div>
                                <div className="w-8 bg-emerald-500 rounded-t-xs h-[75%] text-center text-[10px] text-white">$1980</div>
                                <div className="w-8 bg-emerald-600 rounded-t-xs h-[55%] text-center text-[10px] text-white">$1250</div>
                            </div>
                        </Card>
                    </div>

                    {/* Operational report table */}
                    <Card className="p-6 space-y-4">
                        <h3 className="text-base font-bold text-gray-900 flex items-center">
                            <FileText className="h-5 w-5 text-blue-600 mr-2" /> Operations Ledger
                        </h3>
                        <Table headers={['Report Date', 'Appointments Booked', 'New Admissions', 'Surgeries Performed', 'Revenue Ledger', 'Action']}>
                            {reports.map((rep) => (
                                <tr key={rep.id} className="hover:bg-gray-50">
                                    <td className="px-6 py-4 font-semibold text-gray-900">{rep.date}</td>
                                    <td className="px-6 py-4 font-bold text-gray-700">{rep.bookings} Bookings</td>
                                    <td className="px-6 py-4 font-medium text-gray-700">{rep.admissions} Admits</td>
                                    <td className="px-6 py-4">{rep.surgeries} Surgeries</td>
                                    <td className="px-6 py-4 font-bold text-emerald-600">${rep.revenue.toFixed(2)}</td>
                                    <td className="px-6 py-4">
                                        <Button size="sm" variant="outline" onClick={() => handleExport(rep)}>
                                            Export CSV
                                        </Button>
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
