<?php

namespace App\Controllers;

class Reviews extends BaseController
{
    public function submit()
    {
        if (!session()->get('user_id')) {
            return redirect()->back()->with('error', 'Please log in to submit a review.');
        }

        $productId = $this->request->getPost('product_id');
        $rating = (int)$this->request->getPost('rating');
        $reviewText = $this->request->getPost('review_text');

        if (empty($productId) || $rating < 1 || $rating > 5) {
            return redirect()->back()->with('error', 'Invalid rating data.');
        }

        $db = \Config\Database::connect();
        
        // Verify product exists
        $product = $db->table('products')->where('id', $productId)->get()->getRowArray();
        if (!$product) {
            return redirect()->back()->with('error', 'Product not found.');
        }

        $db->table('product_reviews')->insert([
            'product_id' => $productId,
            'user_id'    => session()->get('user_id'),
            'rating'     => $rating,
            'review_text'=> $reviewText,
            'status'     => 'pending',
            'created_at' => date('Y-m-d H:i:s')
        ]);

        return redirect()->back()->with('success', 'Your review has been submitted and is pending approval!');
    }
}
