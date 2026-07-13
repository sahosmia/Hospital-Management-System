<?php

namespace App\Http\Controllers;

use App\Models\Award;
use App\Models\ContactMessage;
use App\Models\Department;
use App\Models\Doctor;
use App\Models\DoctorSchedule;
use App\Models\Facility;
use App\Models\Faq;
use App\Models\FaqCategory;
use App\Models\News;
use App\Models\NewsCategory;
use App\Models\Notice;
use App\Models\Service;
use App\Models\SystemSetting;
use App\Models\Testimonial;
use App\Models\User;
use App\Models\WorkingHour;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PublicController extends Controller
{
    /**
     * Get system setting value helper.
     */
    protected function getSetting(string $key, string $default = ''): string
    {
        return SystemSetting::where('key', $key)->first()?->value ?? $default;
    }

    /**
     * Public Homepage.
     */
    public function index()
    {
        $settings = [
            'hero_title' => $this->getSetting('hero_title', 'Providing World-Class Medical Care'),
            'hero_subtitle' => $this->getSetting('hero_subtitle'),
            'hero_image' => $this->getSetting('hero_image'),
            'emergency_phone' => $this->getSetting('emergency_phone'),
            'emergency_hours' => $this->getSetting('emergency_hours'),
        ];

        // Database counts
        $counts = [
            'doctors' => Doctor::count(),
            'departments' => Department::count(),
            'patients' => User::where('role', 'patient')->count(),
            'appointments' => \App\Models\Appointment::count(),
        ];

        $featuredDoctors = Doctor::where('is_featured', true)->with(['user', 'department'])->limit(6)->get();
        $services = Service::where('is_active', true)->with('department')->limit(6)->get();
        $testimonials = Testimonial::where('is_approved', true)->limit(3)->get();
        $notices = Notice::where('is_active', true)->latest()->limit(3)->get();

        return view('public.index', compact('settings', 'counts', 'featuredDoctors', 'services', 'testimonials', 'notices'));
    }

    /**
     * Doctor List.
     */
    public function doctors(Request $request)
    {
        $specialties = Department::where('is_active', true)->get();
        $query = Doctor::with(['user', 'department', 'reviews']);

        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->whereHas('user', function($userQuery) use ($search) {
                    $userQuery->where('name', 'like', "%{$search}%");
                })->orWhere('specialization', 'like', "%{$search}%");
            });
        }

        if ($request->has('specialty') && $request->specialty) {
            $query->where('department_id', $request->specialty);
        }

        $doctors = $query->paginate(10);
        $todayDay = strtolower(Carbon::now()->format('l'));

        return view('public.doctors', compact('doctors', 'specialties', 'todayDay'));
    }

    /**
     * Doctor Profile.
     */
    public function doctorProfile(int $id)
    {
        $doctor = Doctor::with(['user', 'department', 'reviews.patient'])->findOrFail($id);
        $schedules = DoctorSchedule::where('doctor_id', $doctor->user_id)->get();
        $reviews = $doctor->reviews()->where('status', 'approved')->with('patient')->latest()->paginate(10);

        // Aggregate ratings break-down
        $ratingsCount = $doctor->reviews()->count();
        $ratingsAvg = $doctor->reviews()->avg('rating') ?? 0;

        $relatedDoctors = Doctor::where('department_id', $doctor->department_id)
            ->where('id', '!=', $id)
            ->with('user')
            ->limit(4)
            ->get();

        return view('public.doctor-profile', compact('doctor', 'schedules', 'reviews', 'ratingsCount', 'ratingsAvg', 'relatedDoctors'));
    }

    /**
     * About Us.
     */
    public function about()
    {
        $settings = [
            'hospital_name' => $this->getSetting('hospital_name', 'St. Jude General Hospital'),
            'hospital_established' => $this->getSetting('hospital_established', '2010'),
            'hospital_description' => $this->getSetting('hospital_description'),
            'mission' => $this->getSetting('mission'),
            'vision' => $this->getSetting('vision'),
            'core_values' => $this->getSetting('core_values'),
        ];

        $facilities = Facility::where('is_active', true)->get();
        $team = User::where('role', 'doctor')->where('is_active', true)->with('doctor')->limit(10)->get();
        $awards = Award::where('is_active', true)->orderBy('year', 'desc')->get();

        return view('public.about', compact('settings', 'facilities', 'team', 'awards'));
    }

    /**
     * Contact Us.
     */
    public function contact()
    {
        $settings = [
            'address' => $this->getSetting('address', 'HMS HQ Street'),
            'phone' => $this->getSetting('phone', '+1 (555) 000-0100'),
            'email' => $this->getSetting('email', 'contact@hms.com'),
            'emergency_phone' => $this->getSetting('emergency_phone'),
            'google_map_embed_url' => $this->getSetting('google_map_embed_url'),
            'facebook_url' => $this->getSetting('facebook_url'),
            'youtube_url' => $this->getSetting('youtube_url'),
            'linkedin_url' => $this->getSetting('linkedin_url'),
            'instagram_url' => $this->getSetting('instagram_url'),
        ];

        $workingHours = WorkingHour::all();

        return view('public.contact', compact('settings', 'workingHours'));
    }

    /**
     * Process Contact message form submission.
     */
    public function contactSubmit(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:100',
            'email' => 'required|email|max:100',
            'phone' => 'nullable|string|max:15',
            'subject' => 'nullable|string|max:255',
            'message' => 'required|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        ContactMessage::create($request->all());

        return redirect()->back()->with('success', 'Your contact message was successfully received. Our front-desk coordinator will reach back to you shortly.');
    }

    /**
     * Services.
     */
    public function services(Request $request)
    {
        $services = Service::where('is_active', true)->with('department')->paginate(12);
        return view('public.services', compact('services'));
    }

    /**
     * Service Details.
     */
    public function serviceDetails(int $id)
    {
        $service = Service::with('department')->findOrFail($id);
        $relatedServices = Service::where('department_id', $service->department_id)
            ->where('id', '!=', $id)
            ->limit(4)
            ->get();

        return view('public.service-details', compact('service', 'relatedServices'));
    }

    /**
     * Departments.
     */
    public function departments()
    {
        $departments = Department::with('doctors.user')->where('is_active', true)->get();
        return view('public.departments', compact('departments'));
    }

    /**
     * News/Blog List.
     */
    public function news()
    {
        $newsList = News::where('is_published', true)->latest()->paginate(9);
        $categories = NewsCategory::withCount('news')->get();

        return view('public.news', compact('newsList', 'categories'));
    }

    /**
     * News Details.
     */
    public function newsDetails(int $id)
    {
        $news = News::with('category')->findOrFail($id);
        $categories = NewsCategory::withCount('news')->get();
        $relatedNews = News::where('category_id', $news->category_id)
            ->where('id', '!=', $id)
            ->limit(3)
            ->get();

        return view('public.news-details', compact('news', 'categories', 'relatedNews'));
    }

    /**
     * FAQ Page.
     */
    public function faq()
    {
        $categories = FaqCategory::with(['faqs' => function($query) {
            $query->where('is_active', true)->orderBy('order');
        }])->where('is_active', true)->get();

        return view('public.faq', compact('categories'));
    }

    /**
     * SEO Terms.
     */
    public function terms()
    {
        $terms = $this->getSetting('terms_and_conditions');
        return view('public.terms', compact('terms'));
    }

    /**
     * SEO Privacy.
     */
    public function privacy()
    {
        $privacy = $this->getSetting('privacy_policy');
        return view('public.privacy', compact('privacy'));
    }

    /**
     * Public Login.
     */
    public function login()
    {
        return view('public.login');
    }

    /**
     * Public Register.
     */
    public function register()
    {
        $terms = $this->getSetting('terms_and_conditions');
        $privacy = $this->getSetting('privacy_policy');
        return view('public.register', compact('terms', 'privacy'));
    }
}
