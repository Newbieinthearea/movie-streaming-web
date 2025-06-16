<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Genre;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class GenreController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
     $genres = Genre::orderBy('name')->paginate(10); // Get genres, order by name, 10 per page
        // This looks for a view at resources/views/admin/genres/index.blade.php
        return view('admin.genres.index', compact('genres'));   //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
        return view('admin.genres.create');
    }

    /**
     * Store a newly created resource in storage.
     */
public function store(Request $request) // Laravel injects the current HTTP Request
{
    // 1. Validate the incoming request data
    $validatedData = $request->validate([
        'name' => 'required|string|max:255|unique:genres,name', // Name is required, string, max 255 chars, unique in 'genres' table
        'slug' => 'nullable|string|max:255|unique:genres,slug', // Slug is optional, but if provided, must be unique
    ]);

    // 2. Prepare data for creation
    $dataToStore = [
        'name' => $validatedData['name'],
    ];

    // 3. Handle the slug: Generate if empty, otherwise use the validated one.
    //    Also ensure uniqueness for auto-generated slugs.
    if (empty($validatedData['slug'])) {
        $baseSlug = Str::slug($validatedData['name']);
        $slug = $baseSlug;
        $counter = 1;
        // Check if the generated slug already exists and append a counter if it does
        while (Genre::where('slug', $slug)->exists()) {
            $slug = $baseSlug . '-' . $counter++;
        }
        $dataToStore['slug'] = $slug;
    } else {
        // If a slug was provided, it has already been validated for uniqueness
        $dataToStore['slug'] = $validatedData['slug'];
    }

    // 4. Create the new Genre using mass assignment
    // Ensure 'name' and 'slug' are in the $fillable array in your Genre model
    Genre::create($dataToStore);

    // 5. Redirect back to the genre index page with a success message
    return redirect()->route('admin.genres.index')
                     ->with('success', 'Genre created successfully!');
}

// ... (show, edit, update, destroy methods are below) ...

    /**
     * Display the specified resource.
     */
    public function show(Genre $genre)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Genre $genre)
    {
        //
        return view('admin.genres.edit', compact('genre'));
    }

/**
 * Update the specified resource in storage.
 */
public function update(Request $request, Genre $genre)
{
    // 1. Validate the incoming request data
    $validatedData = $request->validate([
        'name' => [
            'required',
            'string',
            'max:255',
            Rule::unique('genres', 'name')->ignore($genre->id), // Name must be unique, ignoring the current genre
        ],
        'slug' => [
            'required', // Making slug required for edit, assuming it might have been auto-generated initially
            'string',
            'max:255',
            Rule::unique('genres', 'slug')->ignore($genre->id), // Slug must be unique, ignoring the current genre
        ],
    ]);

    // 2. Update the genre's attributes
    // If you want to re-generate slug based on name if name changes and slug wasn't manually edited,
    // you might add logic here. For simplicity, we'll assume the submitted slug is intended.
    // Or, if you always want slug to be based on name:
    // $validatedData['slug'] = Str::slug($validatedData['name']);
    // (Ensure this new slug is unique, ignoring current genre, perhaps add loop like in store if auto-generating)

    $genre->update($validatedData);

    // 3. Redirect back to the index page with a success message
    return redirect()->route('admin.genres.index')
                     ->with('success', 'Genre updated successfully!');
}
    public function destroy(Genre $genre)
    {
        //
        try {
        // Attempt to delete the genre
        $genre->delete();

        // Redirect back to the index page with a success message
        return redirect()->route('admin.genres.index')
                         ->with('success', 'Genre "' . $genre->name . '" deleted successfully!');

    } catch (\Illuminate\Database\QueryException $e) {
        // Handle potential foreign key constraint violations or other DB errors
        // For example, if genres are linked to movies and the DB enforces this,
        // deletion might fail if movies are still associated with this genre.
        // You might need to implement logic to disassociate movies or prevent deletion.
        return redirect()->route('admin.genres.index')
                         ->with('error', 'Could not delete genre. It might be in use by movies or TV shows.');
    } catch (\Exception $e) {
        // Handle any other unexpected errors
        return redirect()->route('admin.genres.index')
                         ->with('error', 'An unexpected error occurred while trying to delete the genre.');
    }
    }
}
