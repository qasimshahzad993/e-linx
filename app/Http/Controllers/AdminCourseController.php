<?php

namespace App\Http\Controllers;

use App\Models\Course;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminCourseController extends Controller
{
    public function index(Request $request): View
    {
        return view('admin.courses', [
            'courses' => Course::orderBy('sort_order')->orderBy('title')->get(),
            'edit' => $request->integer('edit') ? Course::find($request->integer('edit')) : null,
        ]);
    }

    public function save(Request $request): RedirectResponse
    {
        $id = $request->integer('id');
        $course = $id ? Course::findOrFail($id) : new Course;
        $data = $request->validate([
            'title' => ['required', 'string', 'max:180'],
            'category' => ['nullable', 'string', 'max:100'],
            'summary' => ['required', 'string', 'max:320'],
            'description' => ['required', 'string', 'max:10000'],
            'curriculum' => ['nullable', 'string', 'max:10000'],
            'duration' => ['nullable', 'string', 'max:100'],
            'delivery_mode' => ['nullable', 'string', 'max:100'],
            'price' => ['nullable', 'string', 'max:100'],
            'icon' => ['nullable', 'string', 'max:100'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);
        $data['slug'] = Course::uniqueSlug($data['title'], $course);
        $data['icon'] = filled($data['icon'] ?? null) ? $data['icon'] : 'bi bi-mortarboard';
        $data['sort_order'] = (int) ($data['sort_order'] ?? 0);
        $data['status'] = $request->boolean('status');
        $course->fill($data)->save();

        return redirect()->route('admin.courses.index')->with('success', 'Course saved.');
    }

    public function delete(Course $course): RedirectResponse
    {
        $course->delete();

        return redirect()->route('admin.courses.index')->with('success', 'Course deleted.');
    }
}
