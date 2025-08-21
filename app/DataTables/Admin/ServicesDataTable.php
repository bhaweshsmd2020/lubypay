<?php

namespace App\DataTables\Admin;

use App\Http\Helpers\Common;
use App\Models\Country;
use App\User;
use App\Models\Services;
use Yajra\DataTables\Services\DataTable;

class ServicesDataTable extends DataTable
{
    /**
     * Build DataTable class.
     *
     * @return \Yajra\Datatables\Engines\BaseEngine
     */
    public function ajax() //don't use default dataTable() method
    {
        return datatables()
            ->eloquent($this->query())
            ->addColumn('action', function ($services)
            {
                $edit = $delete = '';
                $edit = (Common::has_permission(\Auth::guard('admin')->user()->id, 'edit_services')) ? '<a href="' . url('admin/settings/services_edit/' . $services->id) . '" class="btn btn-xs btn-primary"><i class="glyphicon glyphicon-edit"></i></a>&nbsp;' : '';

                $delete = (Common::has_permission(\Auth::guard('admin')->user()->id, 'delete_services')) ? '<a href="' . url('admin/settings/services_delete/' . $services->id) . '" class="btn btn-xs btn-danger delete-warning"><i class="glyphicon glyphicon-trash"></i></a>' : '';

                return $edit . $delete;
            })
              ->addColumn('image', function ($services)
            {
                $image ='<img src="'.url('/public/'.$services->image).'" style="width: 40px; height: 40px; border-radius: 50%;">';
                return $image;
            })
            ->rawColumns(['image','action'])
            ->make(true);
    }

    /**
     * Get the query object to be processed by dataTables.
     *
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Query\Builder|\Illuminate\Support\Collection
     */
    public function query()
    {
        // $query = Country::query()->orderBy('id', 'desc');
        $query = Services::orderBy('sorting','ASC')->select();
        return $this->applyScopes($query);
    }

    /**
     * Optional method if you want to use html builder.
     *
     * @return \Yajra\Datatables\Html\Builder
     */
    public function html()
    {
        return $this->builder()
             ->addColumn(['data' => 'id', 'name' => 'services.id', 'title' => 'ID', 'searchable' => true, 'visible' => true])
            ->addColumn(['data' => 'name', 'name' => 'services.name', 'title' => 'Name'])
            ->addColumn(['data' => 'page', 'name' => 'services.page', 'title' => 'Page'])
            ->addColumn(['data' => 'image', 'name' => 'services.number_code', 'title' => 'Image'])
            ->addColumn(['data' => 'position', 'name' => 'services.position', 'title' => 'Position'])
            ->addColumn(['data' => 'status', 'name' => 'services.status', 'title' => 'Status'])
            ->addColumn(['data' => 'sorting', 'name' => 'services.sorting', 'title' => 'Sorting'])
            ->addColumn(['data' => 'action', 'name' => 'action', 'title' => 'Action', 'orderable' => false, 'searchable' => false])
            ->parameters([
                'order'      => [[0, 'desc']],
                //centering all texts in columns
                "columnDefs" => [
                    [
                        "className" => "dt-center",
                        "targets" => "_all"
                    ]
                ],
                'pageLength' => \Session::get('row_per_page'),
                'language'   => \Session::get('language'),
            ]);
    }

    /**
     * Get columns.
     *
     * @return array
     */
    protected function getColumns()
    {
        return [
            'short_name',
            'name',
            'iso3',
            'number_code',
            'phone_code',
            'status',
            'action',
        ];
    }

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename()
    {
        return 'countriesdatatable_' . time();
    }
}