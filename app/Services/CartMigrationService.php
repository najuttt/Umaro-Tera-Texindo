<?php

namespace App\Services;

use App\Models\Guest_carts;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cookie;

class CartMigrationService
{
    /**
     * Migrate guest cart ke user cart setelah login
     */
    public static function migrateGuestCartToUser($userId)
    {
        // Ambil session ID lama dari cookie/session
        $oldSessionId = request()->cookie('guest_session_id') ?? session('guest_session_id');
        
        if (!$oldSessionId) {
            return;
        }

        DB::beginTransaction();
        try {
            // Cari cart guest
            $guestCart = Guest_carts::where('session_id', $oldSessionId)
                ->whereNull('user_id')
                ->first();

            if (!$guestCart) {
                DB::commit();
                return;
            }

            // Cek apakah user sudah punya cart
            $userCart = Guest_carts::where('user_id', $userId)
                ->where('is_locked', false)
                ->first();

            if ($userCart) {
                // MERGE: Pindahkan item dari guest cart ke user cart
                foreach ($guestCart->guestCartItems as $guestItem) {
                    $existingItem = $userCart->guestCartItems()
                        ->where('item_id', $guestItem->item_id)
                        ->first();

                    if ($existingItem) {
                        // Update quantity kalau item sudah ada
                        $existingItem->update([
                            'quantity' => $existingItem->quantity + $guestItem->quantity
                        ]);
                    } else {
                        // Pindahkan item ke user cart
                        $guestItem->update([
                            'guest_cart_id' => $userCart->id
                        ]);
                    }
                }

                // Hapus guest cart yang kosong
                $guestCart->delete();
            } else {
                // Langsung assign cart guest ke user
                $guestCart->update([
                    'user_id' => $userId
                ]);
            }

            // Hapus cookie lama
            Cookie::queue(Cookie::forget('guest_session_id'));
            session()->forget('guest_session_id');

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            \Illuminate\Support\Facades\Log::error('Cart migration failed: ' . $e->getMessage());
        }
    }
}