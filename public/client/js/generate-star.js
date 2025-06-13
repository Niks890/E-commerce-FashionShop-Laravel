function generateStarRating(rating) {
    rating = parseFloat(rating) || 0;
    let stars = '';
    const fullStars = Math.floor(rating);
    const hasHalfStar = rating % 1 >= 0.5;

    // Full stars
    for (let i = 0; i < fullStars; i++) {
        stars += '<i class="fa fa-star text-warning" style="margin-right: 1px;"></i>';
    }

    // Half star
    if (hasHalfStar) {
        stars += '<i class="fa fa-star-half-o" style="color: #ffc107; margin-right: 1px;"></i>';
    }

    // Empty stars
    const emptyStars = 5 - fullStars - (hasHalfStar ? 1 : 0);
    for (let i = 0; i < emptyStars; i++) {
        stars += '<i class="fa fa-star-o" style="margin-right: 1px; color: #ccc;"></i>';
    }

    return stars;
}
