<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AccountConfirmation;
use App\Models\User;
use Illuminate\Http\Request;

class AccountsConfirmationsController extends Controller
{
    public function index($limit = 1, $page = 0)
    {

        $rAC = AccountConfirmation::orderBy('created_at', 'desc')
            ->skip(($page - 1) * $limit)
            ->take($limit)
            ->get();

        return response()->json($rAC, 200);
    }
    public function store(Request $request)
    {
        $this->validate($request, [
            "fullname_in_arabic" => "required",
            "fullname_in_english" => "required",
            "identity_card_front" => "required|image",
            "identity_card_back" => "required|image",
        ]);

        $ac = new AccountConfirmation();
        $path = "Assets/AccountsConfirmations/";

        $idf = $request->file("identity_card_front");
        $idf_extension = $idf->getClientOriginalExtension();
        $idf_fullname = time() . "_" . auth()->id() . "_front." . $idf_extension; // Updated filename for front

        $idb = $request->file("identity_card_back");
        $idb_extension = $idb->getClientOriginalExtension();
        $idb_fullname = time() . "_" . auth()->id() . "_back." . $idb_extension; // Updated filename for back

        $idf->move($path, $idf_fullname);
        $idb->move($path, $idb_fullname);

        $ac = AccountConfirmation::create([
            'owner_id' => auth()->id(),
            'full_name_in_arabic' => $request->fullname_in_arabic,
            'full_name_in_english' => $request->fullname_in_english,
            'id_card_front' => $idf_fullname,
            'id_card_back' => $idb_fullname,
            'status' => false,
        ]);

        return response()->json([
            "request" => true,
            ...$ac->toArray(),
        ], 200);
    }

    public function show()
    {
        $accountConfirmation = AccountConfirmation::where("owner_id", auth()->id());
        if ($accountConfirmation->exists()) {
            return response()->json([
                "request" => true,
                ...$accountConfirmation->first()->toArray(),
            ], 200);
        }

        return response()->json([
            "request" => false,
            "message" => "No account confirmation request found",
        ], 200);
    }

    public function accept($id)
    {
        $accountConfirmation = AccountConfirmation::where("id", $id);
        if (!$accountConfirmation->exists()) {
            return response()->json([
                "request" => false,
                "message" => "No account confirmation request found",
            ], 200);
        }

        $accountConfirmation = $accountConfirmation->first();
        $user = User::find($accountConfirmation->owner_id);
        $user->account_confirmation = true;
        $user->update();
        $accountConfirmation->status = true;
        $accountConfirmation->save();

        return response()->json([
            "request" => true,
            ...$accountConfirmation->toArray(),
        ], 200);
    }

    public function unaccepted($id)
    {
        $accountConfirmation = AccountConfirmation::where("id", $id);
        if (!$accountConfirmation->exists()) {
            return response()->json([
                "request" => false,
                "message" => "No account confirmation request found",
            ], 200);
        }

        $accountConfirmation = $accountConfirmation->first();
        $user = User::find($accountConfirmation->owner_id);
        $user->account_confirmation = false;
        $user->update();
        $accountConfirmation->status = false;
        $accountConfirmation->save();

        return response()->json([
            "request" => true,
            ...$accountConfirmation->toArray(),
        ], 200);
    }

    public function searchByName($search)
    {
        $accountConfirmation = AccountConfirmation::where("full_name_in_arabic", "like", "%$search%")
            ->orWhere("full_name_in_english", "like", "%$search%")
            ->take(10)
            ->get();

        return response()->json($accountConfirmation, 200);
    }

    public function searchByUserId($search)
    {
        $accountConfirmation = AccountConfirmation::where("owner_id", $search)
            ->get();

        if (!$accountConfirmation) {
            return response()->json([
            ], 200);
        }

        return response()->json($accountConfirmation, 200);
    }

    public function destroy($id)
    {
        $accountConfirmation = AccountConfirmation::where("id", $id);
        if (!$accountConfirmation->exists()) {
            return response()->json([
                "request" => false,
                "message" => "No account confirmation request found",
            ], 200);
        }

        $accountConfirmation = $accountConfirmation->first();
        $accountConfirmation->delete();
        $user = User::find($accountConfirmation->owner_id);
        $user->account_confirmation = false;
        $user->update();

        return response()->json([
            "request" => false,
        ], 200);
    }
}
