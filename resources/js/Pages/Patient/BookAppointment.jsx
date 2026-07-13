import React, { useEffect, useState } from 'react';
import { Head, Link } from '@inertiajs/react';
import Sidebar from '../../Components/Sidebar';
import Header from '../../Components/Header';
import Card from '../../Components/Card';
import Button from '../../Components/Button';
import Input from '../../Components/Input';
import Badge from '../../Components/Badge';
import Toast from '../../Components/Toast';
import { Calendar, User, CheckCircle2, ChevronRight, Stethoscope } from 'lucide-react';

export default function BookAppointment() {
    const [step, setStep] = useState(1);
    const [doctors, setDoctors] = useState([]);
    const [selectedDoctor, setSelectedDoctor] = useState(null);
    const [date, setDate] = useState('');
    const [time, setTime] = useState('');
    const [type, setType] = useState('physical');
    const [symptoms, setSymptoms] = useState('');
    const [notes, setNotes] = useState('');
    const [toast, setToast] = useState(null);
    const [availableSlots, setAvailableSlots] = useState([]);

    useEffect(() => {
        setDoctors([
            { id: 3, name: 'Dr. Elizabeth Blackwell', specialty: 'Cardiology', fee: 150.00, chamber: 'Chamber A-101' },
            { id: 4, name: 'Dr. Robert Koch', specialty: 'Neurology', fee: 180.00, chamber: 'Chamber B-205' },
            { id: 5, name: 'Dr. Virginia Apgar', specialty: 'Pediatrics', fee: 120.00, chamber: 'Chamber C-104' }
        ]);
    }, []);

    const handleDoctorSelect = (doc) => {
        setSelectedDoctor(doc);
        setStep(2);
    };

    const handleDateSubmit = (e) => {
        e.preventDefault();
        if (!date) return;

        // Simulating slots generation
        setAvailableSlots([
            { time: '09:00', is_available: true },
            { time: '09:30', is_available: true },
            { time: '10:00', is_available: false },
            { time: '10:30', is_available: true },
            { time: '11:00', is_available: true },
            { time: '11:30', is_available: false }
        ]);
        setStep(3);
    };

    const handleSlotSelect = (slotTime) => {
        setTime(slotTime);
        setStep(4);
    };

    const handleFinalBooking = (e) => {
        e.preventDefault();
        setToast({
            type: 'success',
            message: 'Appointment booked successfully! Serial number: APT-' + date.replace(/-/g, '') + '-' + rand(1000, 9999),
        });
        setTimeout(() => {
            window.location.href = '/patient/appointments';
        }, 2000);
    };

    function rand(min, max) {
        return Math.floor(Math.random() * (max - min + 1) + min);
    }

    return (
        <div className="min-h-screen bg-gray-50 pl-64">
            <Head title="Book Appointment" />
            <Sidebar />

            <div className="flex flex-col min-h-screen">
                <Header title="Schedule Consultation" />

                <main className="flex-1 p-8 space-y-8 max-w-4xl mx-auto w-full">
                    {/* Stepper bar */}
                    <div className="flex items-center space-x-4 border-b border-gray-200 pb-4 mb-8">
                        <span className={`text-sm font-bold ${step === 1 ? 'text-blue-600' : 'text-gray-400'}`}>1. Select Doctor</span>
                        <ChevronRight className="h-4 w-4 text-gray-400" />
                        <span className={`text-sm font-bold ${step === 2 ? 'text-blue-600' : 'text-gray-400'}`}>2. Choose Date</span>
                        <ChevronRight className="h-4 w-4 text-gray-400" />
                        <span className={`text-sm font-bold ${step === 3 ? 'text-blue-600' : 'text-gray-400'}`}>3. Choose Time</span>
                        <ChevronRight className="h-4 w-4 text-gray-400" />
                        <span className={`text-sm font-bold ${step === 4 ? 'text-blue-600' : 'text-gray-400'}`}>4. Confirm details</span>
                    </div>

                    {/* Step 1: Select doctor */}
                    {step === 1 && (
                        <div className="space-y-6">
                            <h3 className="text-lg font-bold text-gray-900">Select Specialist</h3>
                            <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
                                {doctors.map((doc) => (
                                    <Card key={doc.id} variant="hover" className="p-6 space-y-4" onClick={() => handleDoctorSelect(doc)}>
                                        <div className="h-12 w-12 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center font-bold text-xl uppercase">
                                            {doc.name.replace('Dr. ', '').substring(0, 1)}
                                        </div>
                                        <div>
                                            <h4 className="font-bold text-gray-900">{doc.name}</h4>
                                            <p className="text-sm text-gray-500">{doc.specialty}</p>
                                        </div>
                                        <div className="border-t border-gray-100 pt-3 flex justify-between items-center text-sm">
                                            <span className="text-gray-400">Consultation Fee</span>
                                            <span className="font-bold text-blue-600">${doc.fee.toFixed(2)}</span>
                                        </div>
                                    </Card>
                                ))}
                            </div>
                        </div>
                    )}

                    {/* Step 2: Choose date */}
                    {step === 2 && (
                        <Card className="p-8 max-w-md mx-auto space-y-6">
                            <h3 className="text-lg font-bold text-gray-900 text-center">Choose Consultation Date</h3>
                            <p className="text-xs text-center text-gray-500">Selected doctor: <span className="font-semibold text-gray-800">{selectedDoctor?.name}</span></p>
                            <form onSubmit={handleDateSubmit} className="space-y-6">
                                <Input
                                    label="Date"
                                    name="date"
                                    type="date"
                                    min={date || date}
                                    value={date}
                                    onChange={(e) => setDate(e.target.value)}
                                    required
                                />
                                <div className="flex space-x-3">
                                    <Button variant="outline" className="w-1/2" onClick={() => setStep(1)}>Back</Button>
                                    <Button type="submit" variant="primary" className="w-1/2">Next</Button>
                                </div>
                            </form>
                        </Card>
                    )}

                    {/* Step 3: Choose time slot */}
                    {step === 3 && (
                        <Card className="p-8 max-w-lg mx-auto space-y-6">
                            <h3 className="text-lg font-bold text-gray-900 text-center">Available Time Slots</h3>
                            <p className="text-xs text-center text-gray-500">Date: <span className="font-semibold text-gray-800">{date}</span> &bull; Doctor: <span className="font-semibold text-gray-800">{selectedDoctor?.name}</span></p>

                            <div className="grid grid-cols-3 gap-4">
                                {availableSlots.map((slot, idx) => (
                                    <button
                                        key={idx}
                                        disabled={!slot.is_available}
                                        onClick={() => handleSlotSelect(slot.time)}
                                        className={`py-3 rounded-lg border text-sm font-semibold transition-all ${
                                            slot.is_available
                                                ? 'bg-blue-50 border-blue-200 text-blue-800 hover:bg-blue-100 hover:border-blue-300 cursor-pointer'
                                                : 'bg-gray-100 border-gray-200 text-gray-400 cursor-not-allowed'
                                        }`}
                                    >
                                        {slot.time}
                                    </button>
                                ))}
                            </div>

                            <div className="flex space-x-3 pt-6">
                                <Button variant="outline" className="w-1/2" onClick={() => setStep(2)}>Back</Button>
                            </div>
                        </Card>
                    )}

                    {/* Step 4: Confirm details */}
                    {step === 4 && (
                        <Card className="p-8 max-w-xl mx-auto space-y-6">
                            <h3 className="text-lg font-bold text-gray-900 flex items-center justify-center">
                                <CheckCircle2 className="h-6 w-6 text-emerald-500 mr-2" /> Confirm Appointment Details
                            </h3>

                            <div className="bg-gray-50 p-4 rounded-lg space-y-3 text-sm border border-gray-100">
                                <div className="flex justify-between">
                                    <span className="text-gray-500">Consultant Specialist</span>
                                    <span className="font-semibold text-gray-900">{selectedDoctor?.name} ({selectedDoctor?.specialty})</span>
                                </div>
                                <div className="flex justify-between">
                                    <span className="text-gray-500">Chamber Location</span>
                                    <span className="font-semibold text-gray-900">{selectedDoctor?.chamber}</span>
                                </div>
                                <div className="flex justify-between">
                                    <span className="text-gray-500">Schedule Time</span>
                                    <span className="font-semibold text-gray-900">{date} at {time}</span>
                                </div>
                                <div className="flex justify-between border-t border-gray-200 pt-3">
                                    <span className="text-gray-500 font-medium">Consultation Fee</span>
                                    <span className="font-bold text-blue-600">${selectedDoctor?.fee.toFixed(2)}</span>
                                </div>
                            </div>

                            <form onSubmit={handleFinalBooking} className="space-y-4">
                                <Input
                                    label="Visit Type"
                                    name="type"
                                    type="select"
                                    value={type}
                                    onChange={(e) => setType(e.target.value)}
                                    options={[
                                        { value: 'physical', label: 'In-Chamber Physical Visit' },
                                        { value: 'video', label: 'Online Tele-Video Visit' },
                                        { value: 'telephone', label: 'Telephone Visit' },
                                    ]}
                                />

                                <Input
                                    label="Primary Symptoms (Optional)"
                                    name="symptoms"
                                    type="textarea"
                                    placeholder="Briefly describe your symptoms..."
                                    value={symptoms}
                                    onChange={(e) => setSymptoms(e.target.value)}
                                />

                                <div className="flex space-x-3 pt-4">
                                    <Button variant="outline" className="w-1/2" onClick={() => setStep(3)}>Back</Button>
                                    <Button type="submit" variant="success" className="w-1/2">Confirm & Schedule</Button>
                                </div>
                            </form>
                        </Card>
                    )}
                </main>
            </div>

            {toast && (
                <Toast type={toast.type} message={toast.message} onClose={() => setToast(null)} />
            )}
        </div>
    );
}
