<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Slider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Yajra\DataTables\Facades\DataTables;

class SliderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if(request()->ajax())
        {
            $query = Slider::query();

            return DataTables::of($query)
            ->addColumn('action', function($item) {
                return '
                <div class="btn-group">
                    <div class="dropdown">
                        <button class="btn btn-primary dropdown-toggle mr-1 mb-1
                            type="button"
                            data-toggle="dropdown">
                            Aksi
                        </button>
                    <div class="dropdown-menu">
                        <a class="dropdown-item" href="' . route('slider.edit', $item->id) . '">
                            Sunting
                        </a>
                        <form action="'. route('slider.destroy', $item->id) . '" method="POST">
                        '. method_field('delete') . csrf_field() .'
                            <button type="submit" class="dropdown-item text-danger">
                                Hapus
                            </button>
                        </form>
                    </div>
                    </div>
                </div>
               '; 
            })
            ->editColumn('photos', function($item) {
                return $item->photos ? '<img src=" '. Storage::url($item->photos) .'" style="max-height: 80px;" />' : '';
            })
            ->rawColumns(['action', 'photos'])
            ->make();
        }
        return view('pages.admin.slider.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $slider = Slider::all();

        return view('pages.admin.slider.create', [
            'sliders' => $slider
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = $request->all();

        $data['photos'] = $request->file('photos')->store('assets/slider', 'public');

        Slider::create($data);

        return redirect()->route('slider.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $item = Slider::findOrFail($id);

        return view('pages.admin.slider.edit', [
            'item' => $item
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $data = $request->all();
        $data['photos'] = $request->file('photos')->store('assets/slider', 'public');
        $slider = Slider::findOrFail($id);
        $file = $slider->photos;
        $destinationPath = public_path(Storage::url($file));
        unlink($destinationPath);
        
        $slider->update($data);
        return redirect()->route('slider.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $slider = Slider::findOrFail($id);
        $file = $slider->photos;
        $destinationPath = public_path(Storage::url($file));
        unlink($destinationPath);

        $slider->delete();
        return redirect()->route('slider.index');
    }
}
