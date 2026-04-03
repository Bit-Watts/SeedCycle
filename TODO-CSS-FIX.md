# TODO: Full CSS Fix

**Prior:**
- Selector fixed (#login_container in CSS).

**Current Issues (from rendered HTML/curl):**
- Link: `assets\\css\\style.css` → 404 (Windows \ separator).
- Div: `class="login_container"` → No match (CSS uses ID).

**Steps:**
- [x] Step 1: Fix Login.php - `/` paths + ID selector. (Full overwrite)
- [x] Step 2: Update CSS for .login_form if used. (Already .login_form)
- [x] Step 3: curl retest.
- [x] Step 4: Complete.


