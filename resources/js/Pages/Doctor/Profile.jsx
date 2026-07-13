import React, { useEffect, useState } from 'react';
import { Head, Link } from '@inertiajs/react';
import Sidebar from '../../Components/Sidebar';
import Header from '../../Components/Header';
import Card from '../../Components/Card';
import Badge from '../../Components/Badge';
import { Star, Calendar, FileText } from 'lucide-react';

export default function DoctorProfile() {
    const [doctor, setDoctor] = useState(null);
    const [reviews, setReviews] = useState([]);

    useEffect(() => {
        setDoctor({
            id: 3,
            name: 'Dr. Elizabeth Blackwell',
            specialty: 'Cardiology',
            fee: 150.00,
            experience: 12,
            chamber: 'Chamber A-101',
            schedules: [
                { day_of_week: 'Monday', start_time: '09:00 AM', end_time: '01:00 PM' },
                { day_of_week: 'Wednesday', start_time: '09:00 AM', end_time: '01:00 PM' }
            ]
        });

        setReviews([
            { id: 1, rating: 5, reviewer: 'Ada Lovelace', comment: 'Extremely professional and knowledgeable. Highly recommended!' },
            { id: 2, rating: 4, reviewer: 'Alan Turing', comment: 'Good communication and brief waiting time.' }
        ]);
    }, []);

    if (!doctor) return null;

    return (
        <div className="min-h-screen bg-gray-50 pl-64">
            <Head title={`Doctor Profile: ${doctor.name}`} />
            <Sidebar />

            <div className="flex flex-col min-h-screen">
                <Header title={`Doctor Profile: ${doctor.name}`} />

                <main className="flex-1 p-8 space-y-8 max-w-5xl mx-auto w-full">
                    <div className="grid grid-cols-1 lg:grid-cols-3 gap-8">
                        {/* Profile Summary card */}
                        <div className="space-y-6">
                            <Card className="p-6 text-center space-y-4">
                                <div className="h-20 w-24 rounded-full bg-blue-100 text-blue-700 flex items-center justify-center font-bold text-3xl mx-auto uppercase">
                                    EB
                                </div>
                                <div>
                                    <h3 className="text-lg font-bold text-gray-900">{doctor.name}</h3>
                                    <p className="text-sm text-gray-500 font-semibold">{doctor.specialty} Specialist</p>
                                </div>
                                <div className="border-t border-gray-100 pt-4 flex justify-between items-center text-sm">
                                    <span className="text-gray-400">Consultation Fee</span>
                                    <span className="font-bold text-blue-600">${doctor.fee.toFixed(2)}</span>
                                </div>
                            </Card>

                            <Link href="/patient/book" className="block">
                                <button className="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold text-sm py-3 rounded-md transition-all shadow-xs">
                                    Book Live Appointment
                                </button>
                            </Link>
                        </div>

                        {/* Schedules & Reviews on Right Column */}
                        <div className="lg:col-span-2 space-y-8">
                            {/* Schedule info */}
                            <Card className="p-6 space-y-4">
                                <h3 className="text-base font-bold text-gray-900 flex items-center">
                                    <Calendar className="h-5 w-5 text-blue-600 mr-2" /> Consulting Work Schedules
                                </h3>
                                <div className="space-y-3">
                                    {doctor.schedules.map((sch, idx) => (
                                        <div key={idx} className="flex justify-between items-center p-3 bg-gray-50 rounded-lg text-sm">
                                            <span className="font-semibold text-gray-800 capitalize">{sch.day_of_week}</span>
                                            <span className="text-gray-600">{sch.start_time} - {sch.end_time}</span>
                                        </div>
                                    ))}
                                </div>
                            </Card>

                            {/* Review logs */}
                            <div className="space-y-4">
                                <div className="flex justify-between items-center">
                                    <h3 className="text-base font-bold text-gray-900 flex items-center">
                                        <Star className="h-5 w-5 text-amber-500 mr-2" /> Recent Patient Feedbacks
                                    </h3>
                                    <Link href="/doctor/reviews/new" className="text-xs font-semibold text-blue-600 hover:text-blue-500">
                                        Submit Feedback
                                    </Link>
                                </div>
                                <div className="space-y-4">
                                    {reviews.map((rev) => (
                                        <Card key={rev.id} className="p-5 space-y-3">
                                            <div className="flex justify-between items-center">
                                                <span className="font-semibold text-gray-900 text-sm">{rev.reviewer}</span>
                                                <span className="text-amber-500 flex items-center text-xs font-bold">
                                                    <Star className="h-3.5 w-3.5 fill-current mr-1" /> {rev.rating} / 5
                                                </span>
                                            </div>
                                            <p className="text-sm text-gray-600 leading-relaxed italic">
                                                "{rev.comment}"
                                            </p>
                                        </Card>
                                    ))}
                                </div>
                            </div>
                        </div>
                    </div>
                </main>
            </div>
        </div>
    );
}
