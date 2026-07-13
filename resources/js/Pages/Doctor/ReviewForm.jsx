import React, { useState } from 'react';
import { Head } from '@inertiajs/react';
import Sidebar from '../../Components/Sidebar';
import Header from '../../Components/Header';
import Card from '../../Components/Card';
import Input from '../../Components/Input';
import Button from '../../Components/Button';
import Toast from '../../Components/Toast';
import { Star } from 'lucide-react';

export default function ReviewForm() {
    const [rating, setRating] = useState(5);
    const [review, setReview] = useState('');
    const [anonymous, setAnonymous] = useState(false);
    const [toast, setToast] = useState(null);

    const handleSubmit = (e) => {
        e.preventDefault();
        setToast({ type: 'success', message: 'Thank you for your feedback! Review is now pending approval.' });
        setTimeout(() => {
            window.location.href = '/patient/dashboard';
        }, 1500);
    };

    return (
        <div className="min-h-screen bg-gray-50 pl-64">
            <Head title="Submit Doctor Feedback" />
            <Sidebar />

            <div className="flex flex-col min-h-screen">
                <Header title="Submit Doctor Feedback" />

                <main className="flex-1 p-8 space-y-8 max-w-2xl mx-auto w-full">
                    <Card className="p-8 space-y-6">
                        <h3 className="text-lg font-bold text-gray-900 text-center">Consultation Rating</h3>
                        
                        <form onSubmit={handleSubmit} className="space-y-6">
                            <div className="flex justify-center space-x-2">
                                {[1, 2, 3, 4, 5].map((star) => (
                                    <button
                                        key={star}
                                        type="button"
                                        onClick={() => setRating(star)}
                                        className="focus:outline-hidden"
                                    >
                                        <Star className={`h-8 w-8 ${star <= rating ? 'text-amber-500 fill-current' : 'text-gray-300'}`} />
                                    </button>
                                ))}
                            </div>

                            <Input
                                label="Share your experience"
                                name="review"
                                type="textarea"
                                placeholder="Describe the behavior, expertise, or chamber conditions..."
                                value={review}
                                onChange={(e) => setReview(e.target.value)}
                                required
                            />

                            <label className="flex items-center text-sm font-medium text-gray-700">
                                <input
                                    type="checkbox"
                                    checked={anonymous}
                                    onChange={(e) => setAnonymous(e.target.checked)}
                                    className="h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500 mr-2"
                                />
                                Post anonymously
                            </label>

                            <Button type="submit" variant="primary" className="w-full">
                                Submit Feedback & Sync Statistics
                            </Button>
                        </form>
                    </Card>
                </main>
            </div>

            {toast && (
                <Toast type={toast.type} message={toast.message} onClose={() => setToast(null)} />
            )}
        </div>
    );
}
