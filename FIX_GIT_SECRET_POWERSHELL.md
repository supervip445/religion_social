# Fix Git Secret Issue - PowerShell Commands

## Problem
Service account file `service_account/notification-88e7c-c50770d57b15.json` is in commit `f444a7b`.

## Solution: Remove from Git History

### Step 1: Abort any ongoing rebase
```powershell
git rebase --abort
```

### Step 2: Remove file from Git tracking (if it exists)
```powershell
git rm --cached service_account/notification-88e7c-c50770d57b15.json
```

### Step 3: Use git filter-branch (PowerShell compatible)

**Single line command:**
```powershell
git filter-branch --force --index-filter "git rm --cached --ignore-unmatch service_account/notification-88e7c-c50770d57b15.json" --prune-empty --tag-name-filter cat -- --all
```

**Or use git filter-repo (better, but needs installation):**
```powershell
# Install git-filter-repo first: pip install git-filter-repo
git filter-repo --path service_account/notification-88e7c-c50770d57b15.json --invert-paths
```

### Step 4: Clean up and force push
```powershell
# Clean up
git reflog expire --expire=now --all
git gc --prune=now --aggressive

# Force push (WARNING: Rewrites history!)
git push origin main --force
```

## Alternative: Simpler Approach (If only 2-3 commits)

### Option A: Interactive Rebase
```powershell
# Start interactive rebase
git rebase -i 75fa273^

# In the editor, change 'pick' to 'edit' for commit f444a7b
# Then for each commit:
git rm --cached service_account/notification-88e7c-c50770d57b15.json
git commit --amend --no-edit
git rebase --continue

# Force push
git push origin main --force
```

### Option B: Create New Branch Without the File
```powershell
# Create new branch from before the problematic commit
git checkout 75fa273
git checkout -b main-clean

# Cherry-pick commits without the service account file
git cherry-pick f444a7b --strategy-option=theirs
# Manually remove the file if it appears
git rm --cached service_account/notification-88e7c-c50770d57b15.json
git commit --amend

# Continue with other commits
git cherry-pick 31f08fc
git cherry-pick aab4d37

# Replace main branch
git branch -M main
git push origin main --force
```

## Recommended: Use GitHub's Secret Scanning Unblock

If you just want to push without removing from history:
1. Go to: https://github.com/aiworld2048/parayana_dhamma_center/security/secret-scanning/unblock-secret/37C0cPUVU2Emdic2D2wnp7YS5eY
2. Click "Allow secret" (temporary solution)
3. Then remove from history properly later

## After Fixing

1. ✅ Verify file is not tracked: `git ls-files | Select-String notification`
2. ✅ Regenerate service account key in Firebase Console
3. ✅ Update `storage/app/firebase/notification-88e7c-c50770d57b15.json` with new key
4. ✅ Never commit service account files again

