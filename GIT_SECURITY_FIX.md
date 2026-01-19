# Git Security Fix - Service Account Credentials

## Issue
GitHub Push Protection detected a Google Cloud Service Account JSON file containing private keys in your commit. This is a security risk and should never be committed to Git.

## Files to Remove from Git

The following files contain sensitive credentials and should NOT be in Git:
- `service_account/notification-88e7c-c50770d57b15.json`
- `storage/app/firebase/notification-88e7c-c50770d57b15.json` (if committed)

## Solution

### Step 1: Remove from Git History

**Option A: Remove specific file from last commit (if not pushed yet):**
```bash
git rm --cached service_account/notification-88e7c-c50770d57b15.json
git commit --amend -m "Remove service account file from commit"
```

**Option B: Remove from Git history completely:**
```bash
# Remove file from Git tracking
git rm --cached service_account/notification-88e7c-c50770d57b15.json

# If already pushed, remove from history (use with caution!)
git filter-branch --force --index-filter \
  "git rm --cached --ignore-unmatch service_account/notification-88e7c-c50770d57b15.json" \
  --prune-empty --tag-name-filter cat -- --all

# Force push (WARNING: This rewrites history!)
git push origin --force --all
```

**Option C: Use BFG Repo-Cleaner (Recommended for large repos):**
```bash
# Download BFG from https://rtyley.github.io/bfg-repo-cleaner/
java -jar bfg.jar --delete-files notification-88e7c-c50770d57b15.json
git reflog expire --expire=now --all
git gc --prune=now --aggressive
```

### Step 2: Verify .gitignore

✅ Already added to `.gitignore`:
- `/service_account/`
- `/storage/app/firebase/`
- `**/*-firebase-adminsdk-*.json`

### Step 3: Re-add Service Account File Locally (Not in Git)

The service account file should be:
1. **Stored locally** on your development machine
2. **Stored on server** in `storage/app/firebase/` directory
3. **NOT committed to Git**

**For local development:**
- Place the file at: `storage/app/firebase/notification-88e7c-c50770d57b15.json`
- This path is already in `.gitignore`

**For production server:**
- Upload the file directly to the server (via SFTP, SCP, etc.)
- Do NOT commit it to Git

### Step 4: Update GitHub Secret (If Already Pushed)

If the secret was already pushed to GitHub:
1. Go to: https://github.com/aiworld2048/parayana_dhamma_center/security/secret-scanning/unblock-secret/37C0cPUVU2Emdic2D2wnp7YS5eY
2. Or rotate/regenerate the service account key in Firebase Console
3. Remove the old key from Git history

### Step 5: Verify Fix

```bash
# Check if file is still tracked
git ls-files | grep notification-88e7c-c50770d57b15.json

# Should return nothing (file not tracked)
```

## Important Security Notes

1. **Never commit service account JSON files** - They contain private keys
2. **Never commit `google-services.json`** - Contains API keys (though less sensitive)
3. **Use environment variables** for sensitive data when possible
4. **Rotate keys** if they were exposed in Git history

## Current Status

✅ `.gitignore` updated to exclude service account files
✅ Service account file should be stored locally, not in Git
⚠️ Need to remove from Git history if already committed

## Next Steps

1. Remove the file from Git history (use one of the options above)
2. Verify the file is not tracked: `git ls-files | grep notification`
3. Push again: `git push origin main`
4. Store the service account file locally on your machine and server (not in Git)

