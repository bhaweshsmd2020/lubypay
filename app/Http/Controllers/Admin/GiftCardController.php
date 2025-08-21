<?php

namespace App\Http\Controllers\Admin;

use App\DataTables\Admin\GiftCardDataTable;
use App\Http\Controllers\Controller;
use App\Http\Helpers\Common;
use App\Models\GiftCard;
use Illuminate\Http\Request;

class GiftCardController extends Controller
{
    protected $helper;

    public function __construct()
    {
        $this->helper = new Common();
    }

    public function index()
    {
        $data['menu']     = 'users';
        $data['sub_menu'] = 'gift-card';
        $data['cards'] = GiftCard::orderBy('id', 'desc')->get();
        GiftCard::where('read_status', '0')->update(['read_status' => '1']);
        return view('admin.giftcard.view', $data);
    }

    public function add(Request $request)
    {
        if (!$_POST)
        {
            $data['menu']     = 'users';
            $data['sub_menu'] = 'gift-card';
            return view('admin.giftcard.add', $data);
        }
        else if ($_POST)
        {
            $this->validate($request, [
                'country_id'            => 'required|numeric',
                'user_id'               => 'required|numeric',
                'card_number'           => 'required',
                'card_expiry'           => 'required|max:3',
                'card_cvv'              => 'required|numeric',
                'currency_id'           => 'required|numeric',
                'amount'                => 'required|numeric',
                'issue_date'            => 'required|numeric',
                'validity'              => 'required',
                'status'                => 'numeric',
            ]);

            $giftcard                       = new GiftCard();
            $giftcard->country_id           = $request->country_id;
            $giftcard->user_id              = $request->user_id;
            $giftcard->card_number          = $request->card_number;
            $giftcard->card_expiry          = $request->card_expiry;
            $giftcard->card_cvv             = $request->card_cvv;
            $giftcard->currency_id          = $request->currency_id;
            $giftcard->amount               = $request->amount;
            $giftcard->issue_date           = $request->issue_date;
            $giftcard->validity             = $request->validity;
            $giftcard->status               = $request->status;
            $giftcard->save();
            $this->helper->one_time_message('success', 'Gift Card Added Successfully');
            return redirect('admin/card/gift-card');
        }

    }

    public function edit($id)
    {
        $data['menu']     = 'users';
        $data['sub_menu'] = 'gift-card';
        $data['card'] = GiftCard::find($id);
        return view('admin.giftcard.edit', $data);
    }

    public function delete(Request $request)
    {
        Electricity::find($request->id)->delete();
        $this->helper->one_time_message('success', 'Request Deleted Successfully');
        return redirect('admin/card/gift-card');
    }
}
