@extends('admin_master')
@section('content')
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Add Product</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{URL::to('/dashboard')}}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{URL::to('/products')}}">All Slider
                                </a></li>
                        <li class="breadcrumb-item active">Add Product</li>
                    </ol>
                </div><!-- /.col -->
            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <section class="content">
        <div class="card card-primary">
            <div class="card-header">
                <h3 class="card-title">Add Product</h3>
            </div>
            <!-- /.card-header -->
            <!-- form start -->
            <form action="{{route('products.store')}}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="product_name">Product Name <span class="required">*</span></label>
                                <input type="text" name="product_name" class="form-control" id="product_name"
                                    placeholder="Product Name" required="" value="{{old('product_name')}}">
                                @error('product_name')
                                <span class="alert alert-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="product_price">Product Price (BDT) <span class="required">*</span></label>
                                <input type="text" name="product_price" class="form-control numericInput" id="product_price"
                                    placeholder="Product Price" required="" value="{{old('product_price')}}">
                                @error('product_price')
                                <span class="alert alert-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="discount">Discount (%)</label>
                                <input type="text" name="discount" class="form-control numericInput" id="discount"
                                    placeholder="Discount" value="{{old('discount')}}">
                                @error('discount')
                                <span class="alert alert-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div> 


                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="stock_qty">Stock Quantity <span class="required">*</span></label>
                                <input type="text" name="stock_qty" class="form-control numericInput" id="stock_qty"
                                    placeholder="Stock Quantity" value="{{old('stock_qty')}}" required="">
                                @error('stock_qty')
                                <span class="alert alert-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="unit_id">Select Product Unit <span class="required">*</span></label>
                                <select class="form-control select2bs4" name="unit_id" id="unit_id" required="">
                                    <option value="" selected="" disabled="">Select Product Unit</option>
                                    @foreach(units() as $unit)
                                     <option value="{{$unit->id}}">{{$unit->title}}</option>
                                    @endforeach
                                </select>
                                @error('unit_id')
                                <span class="alert alert-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                         


                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="status">Select Status <span class="required">*</span></label>
                                <input type="text" class="form-control" name="status" id="status" readonly="" placeholder="Status" value="Inactive" required=""> 
                                @error('status')
                                <span class="alert alert-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-12">
                          <div class="form-group">
                            <label for="description">Description <span class="required">*</span></label>  
                            <textarea class="form-control description" name="description">{!!old('description')!!}</textarea>
                            @error('description')
                                <span class="alert alert-danger">{{ $message }}</span>
                            @enderror
                          </div>  
                        </div>

                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Gallery Images <span class="required">*</span></label>
                                <div class="drop-container">
                                    <label for="file-input" class="upload-button">Upload Images</label>
                                    <div class="preview-images" id="preview-container"></div>
                                    <input type="file" class="form-control" name="gallery_images[]" id="file-input" multiple>
                                </div>
                                @error('gallery_images')
                                 <span class="alert alert-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div> 

                        
                        <div class="form-group w-100 px-2">
                            <button type="submit" class="btn btn-primary">Submit</button>
                        </div>
                    </div>
                    <!-- /.card-body -->
                </div>
            </form>
        </div>
    </section>
</div>
@endsection

@push('scripts')
  
  <script src="{{asset('custom/multiple_files.js')}}"></script>

@endpush