# WooCommerce Product Gallery Grid

A modern, responsive replacement for the default WooCommerce single product gallery.

This plugin provides a clean, conversion-focused product gallery with a desktop grid layout, a mobile carousel, and a fullscreen modal experience.

---

## ✨ Features

- Overrides the default WooCommerce product gallery using hooks (no template overrides)
- Responsive layout:
  - 🖥️ Desktop → CSS Grid (Airbnb-style layout)
  - 📱 Mobile → Touch-friendly slider (one image per view)
- “Show all photos” overlay when gallery contains more than 5 images
- Fullscreen modal gallery powered by PhotoSwipe:
  - Keyboard navigation
  - Image counter
  - Zoom support
  - Loop
  - Smooth animations
- SplideJS mobile carousel
- Variation image support
- Lazy loading & responsive images (`srcset`)
- No jQuery dependency
- Translation ready
- Update-safe WooCommerce integration

---

## 🧱 Layout Behavior

### Desktop (≥ 1024px)

```
[ Large Image ][ Small Image ]
[ Large Image ][ Small Image ]
```

- First image spans two rows
- Next four images displayed in a 2×2 grid
- If more than five images exist → “Show all photos” overlay appears

### Mobile (< 1024px)

- One image per slide
- Swipe navigation
- Pagination dots
- Auto height
- Smooth touch interaction

---

## 🔍 Modal Gallery

- Opens when any image is clicked
- Starts from the clicked image
- Fullscreen supported
- Zoom & pan gestures
- Image index counter
- Arrow navigation
- ESC to close

---

## ⚙️ Installation

1. Download or clone this repository into:

   ```
   wp-content/plugins/
   ```

2. Activate the plugin from the WordPress admin panel.

---

## 🧩 Requirements

- WordPress 6.0+
- WooCommerce 7.0+

---

## 🪝 Available Filters

```php
apply_filters( 'wpgg_breakpoint', 1024 );
apply_filters( 'wpgg_max_grid_images', 5 );
```

---

## 🧑‍💻 Developer Notes

### Gallery Integration

The default WooCommerce gallery is removed using:

```php
remove_action(
  'woocommerce_before_single_product_summary',
  'woocommerce_show_product_images',
  20
);
```

The custom gallery is injected into the same hook position for full compatibility.

### Performance

- Assets are only loaded on single product pages
- Uses WooCommerce image sizes
- Uses `wp_get_attachment_image()` for responsive output
- Mobile slider is only initialized below the defined breakpoint

### JavaScript

- Vanilla JS (no jQuery)
- SplideJS for mobile slider
- PhotoSwipe 5 for modal

---

## 🎨 Tech Stack

- CSS Grid
- Splide.js
- PhotoSwipe 5
- Vanilla JavaScript
- WooCommerce hooks & filters

---

## 📁 Plugin Structure

```
woocommerce-product-gallery-grid/
│── woocommerce-product-gallery-grid.php
│── includes/
│── templates/
│── assets/
│   ├── css/
│   └── js/
```

---

## 🚀 Roadmap

- Thumbnails inside modal
- Video support in product gallery
- Dynamic grid based on image count
- Gutenberg product gallery block
- Container-aware layout for themes like GeneratePress
- Optional fullscreen toggle button

---

## 🤝 Contributing

Pull requests are welcome.

For major changes, please open an issue first to discuss your proposal.

---

## 👤 Author

Steven Ayo

---

## 📄 License

GPL-2.0-or-later