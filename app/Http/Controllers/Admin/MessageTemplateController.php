<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Domains\Message\Models\MessageTemplate;
use Illuminate\Http\Request;

class MessageTemplateController extends Controller
{
    public function index()
    {
        $templates = MessageTemplate::latest()->paginate(10);
        return view('pages.template.index', compact('templates'));
    }

    public function create()
    {
        return view('pages.template.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'content' => 'required|string',
            'type' => 'required|in:general,broadcast,reminder',
        ]);

        // Auto-detect variables from content (e.g. {name})
        preg_match_all('/\{([a-zA-Z0-9_]+)\}/', $validated['content'], $matches);
        $variables = array_unique($matches[1]);
        
        $template = MessageTemplate::create([
            'name' => $validated['name'],
            'content' => $validated['content'],
            'type' => $validated['type'],
            'variables' => $variables,
        ]);

        return redirect()->route('admin.templates.index')->with('success', 'Template berhasil dibuat.');
    }

    public function edit(MessageTemplate $template)
    {
        return view('pages.template.edit', compact('template'));
    }

    public function update(Request $request, MessageTemplate $template)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'content' => 'required|string',
            'type' => 'required|in:general,broadcast,reminder',
        ]);

        // Auto-detect variables
        preg_match_all('/\{([a-zA-Z0-9_]+)\}/', $validated['content'], $matches);
        $variables = array_unique($matches[1]);

        $template->update([
            'name' => $validated['name'],
            'content' => $validated['content'],
            'type' => $validated['type'],
            'variables' => $variables,
        ]);

        return redirect()->route('admin.templates.index')->with('success', 'Template berhasil diperbarui.');
    }

    public function destroy(MessageTemplate $template)
    {
        $template->delete();
        return redirect()->route('admin.templates.index')->with('success', 'Template berhasil dihapus.');
    }
}
