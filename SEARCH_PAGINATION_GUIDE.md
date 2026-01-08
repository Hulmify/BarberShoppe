# Quick Reference Guide: Search & Pagination Features

## For Administrators (Quick Reserve Page)

### Accessing the Page
1. Log in to your admin dashboard
2. Click on **"Quick Reserve"** in the navigation menu
3. You'll see the booking form with the new search and pagination features

### Using the Search Feature
1. **Location**: The search bar is located at the top of the "Select Services" section
2. **How to use**: 
   - Simply start typing the name of a service (e.g., "haircut")
   - Results filter instantly as you type
   - The search looks through both service names and descriptions
3. **Clearing search**: Delete all text to see all services again

### Using Pagination
1. **When it appears**: Pagination controls show up when you have more than 10 services
2. **Navigation**: 
   - Click on page numbers (1, 2, 3, etc.) to jump to that page
   - Use "Next" to go to the next page
   - Use "Previous" to go back
3. **Note**: Your selected services remain selected even when changing pages

### Tips
- Search is case-insensitive (typing "HAIRCUT" or "haircut" works the same)
- You can search while on any page
- Selected services show in the summary card on the right

---

## For Customers (Public Booking Page)

### Accessing the Page
1. Visit your booking link (e.g., `yoursite.com/book/your-shop-name`)
2. You'll see the service selection step with search and pagination

### Using the Search Feature
1. **Location**: Search bar at the top of the services section
2. **How to use**:
   - Type keywords related to the service you want
   - Services filter automatically
   - Search includes service names and descriptions
3. **Example searches**:
   - "beard" - shows all beard-related services
   - "shave" - shows shaving services
   - "30" - might show services that are 30 minutes long

### Using Pagination
1. **Navigation**: Click page numbers at the bottom of the services list
2. **Selecting services**: 
   - Click on any service card to select it
   - Selected services show a checkmark
   - Your selections persist across pages

### Booking Flow
1. **Step 1**: Search/browse and select your desired services
2. **Step 2**: Choose date and time (appears after selecting services)
3. **Step 3**: Enter your contact details
4. **Step 4**: Confirm your booking

---

## Keyboard Shortcuts

### Search Bar
- **Tab**: Move focus to/from search bar
- **Escape**: Clear search (when focused on search bar)
- **Enter**: Submit search (though filtering happens automatically)

---

## Troubleshooting

### "No services found" message
- **Cause**: Your search didn't match any services
- **Solution**: Try different keywords or clear the search

### Pagination not showing
- **Cause**: You have 10 or fewer services
- **Solution**: This is normal - pagination only appears when needed

### Search not working
- **Cause**: JavaScript might be disabled
- **Solution**: Enable JavaScript in your browser settings

### Selected services disappear
- **Cause**: This shouldn't happen, but if it does:
- **Solution**: Refresh the page and start over

---

## Technical Notes

### For Developers

**Search Implementation**:
- Client-side filtering using JavaScript
- Searches `data-name` and `data-desc` attributes
- Case-insensitive matching using `toLowerCase()`

**Pagination Implementation**:
- Server-side pagination using Laravel's `paginate(10)`
- Query string preservation with `withQueryString()`
- Conditional rendering with `@if($services->hasPages())`

**Browser Compatibility**:
- Works in all modern browsers
- Requires JavaScript enabled
- Mobile-responsive design

---

## API Endpoints

### Quick Reserve (Admin)
- **URL**: `/admin/pos`
- **Method**: GET
- **Query Parameters**:
  - `search` (optional): Search term
  - `page` (optional): Page number

### Public Booking
- **URL**: `/book/{shop-slug}`
- **Method**: GET
- **Query Parameters**:
  - `search` (optional): Search term
  - `page` (optional): Page number

---

## Support

If you encounter any issues:
1. Clear your browser cache
2. Try a different browser
3. Check the browser console for errors (F12 → Console tab)
4. Contact your system administrator

---

**Last Updated**: January 8, 2026
