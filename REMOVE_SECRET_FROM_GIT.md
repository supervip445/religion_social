# Remove Service Account from Git History

## Current Situation
- Service account file is in commits: `f444a7b` and `75fa273`
- File is NOT currently tracked (good!)
- But it's in Git history, so GitHub blocks the push

## Solution: Remove from Git History

### Option 1: Interactive Rebase (Recommended - if commits are recent)

```bash
# Start interactive rebase from before the problematic commits
git rebase -i HEAD~3

# In the editor, change the commits that contain the service account file
# Change 'pick' to 'edit' for commits f444a7b and 75fa273

# For each commit being edited:
git rm --cached service_account/notification-88e7c-c50770d57b15.json
git commit --amend --no-edit
git rebase --continue

# Force push (WARNING: Rewrites history)
git push origin main --force
```

### Option 2: Remove from Specific Commits (Easier)

```bash
# Remove file from Git tracking
git rm --cached service_account/notification-88e7c-c50770d57b15.json

# Amend the last commit (if it's the most recent)
git commit --amend -m "Remove service account file"

# If you need to remove from older commits, use filter-branch:
git filter-branch --force --index-filter \
  "git rm --cached --ignore-unmatch service_account/notification-88e7c-c50770d57b15.json" \
  --prune-empty --tag-name-filter cat -- f444a7b^..HEAD

# Force push
git push origin main --force
```

### Option 3: Use BFG Repo-Cleaner (Best for large repos)

1. Download BFG: https://rtyley.github.io/bfg-repo-cleaner/
2. Run:
```bash
java -jar bfg.jar --delete-files notification-88e7c-c50770d57b15.json
git reflog expire --expire=now --all
git gc --prune=now --aggressive
git push origin main --force
```

## After Removing from History

1. ✅ Verify file is not tracked: `git ls-files | grep notification` (should return nothing)
2. ✅ Verify .gitignore includes service account files
3. ✅ Commit the .gitignore changes
4. ✅ Push again: `git push origin main`

## Important: Regenerate Service Account Key

Since the private key was exposed in Git history, you should:
1. Go to Firebase Console → Service Accounts
2. Delete the old service account key
3. Generate a new one
4. Update `storage/app/firebase/notification-88e7c-c50770d57b15.json` with the new key

## Quick Fix (If commits are recent and not pushed to others)

```bash
# Remove from last commit
git rm --cached service_account/notification-88e7c-c50770d57b15.json
git commit --amend -m "Remove service account file from commit"

# Add .gitignore changes
git add .gitignore dhamma_apk/.gitignore
git commit -m "Add service account files to .gitignore"

# Force push
git push origin main --force
```

