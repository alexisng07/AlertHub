# 🐞 BUG_REPORT.md

This document contains verified production issues mapped to AH-101 through AH-105, including root causes, fixes, and impact analysis.

---

## AH-101 — Metrics Dashboard Shows Wrong Numbers

### Root Cause
Metrics aggregation logic did not scope queries by `project_id`. Additionally, cache keys were generated using only the date, causing cross-project cache collisions.

Affected file:
`app/AlertMetrics/MetricsAggregator.php`

### Fix Applied
- Added `project_id` filtering in all alert aggregation queries
- Updated cache key to include `project_id` + `date`

Example fix:
cache_key = alert-metrics:{project_id}:{date}


### Impact
- Cross-project metric leakage
- Inconsistent dashboard values between refreshes

---

## AH-102 — Webhooks Create Duplicate or Missing Subscribers

### Root Cause
Incoming webhook processing was not idempotent and lacked deduplication logic. Under high concurrency, multiple webhook events for the same `external_id` were processed simultaneously, causing:
- Duplicate subscriber creation
- Missing subscriber creation when race conditions caused transaction conflicts

Affected component:
Webhook ingestion + Subscriber creation service

### Fix Applied
- Introduced idempotency key using `external_id`
- Added database-level unique constraint on `(external_id, project_id)`
- Implemented transactional lock during subscriber creation

### Impact
- Duplicate subscriber records
- Lost subscriber creation during traffic spikes
- Inconsistent alert delivery

---

## AH-103 — Alert Digests Missing Delivery Window

### Root Cause
Digest scheduling logic did not properly compute or assign `scheduled_window`. Business rules for prioritizing digest timing were not implemented in scheduler.

Expected behavior:
- High-volume alerts → `immediate`
- Low-volume alerts → `next_batch`

### Fix Applied
- Implemented scheduling classification logic in digest scheduler
- Added mapping for `scheduled_window` based on alert volume thresholds

### Impact
- All digests defaulted to null scheduling window
- Incorrect delivery prioritization
- Reduced system predictability for digest timing

---

## AH-104 — Only First Digest Processed Per Subscriber

### Root Cause
Digest job processing lacked concurrency control per subscriber. Multiple jobs dispatched within a short time window competed, but only the first acquired processing lock.

Additionally, no proper queue deduplication or retry-safe locking was implemented.

### Fix Applied
- Introduced per-subscriber distributed lock (10-second window)
- Added idempotent digest job key
- Improved queue retry handling for overlapping jobs

### Impact
- Only first digest executed during burst traffic
- Subsequent digests silently dropped
- Data loss during incident spikes

---

## AH-105 — Engagement Score Inconsistency Between API and Digests

### Root Cause
Two different computation sources were used for engagement score:
- API used realtime computed score
- Digest scheduler cached and overwrote score into shared storage

This caused stale or overwritten values depending on last execution source.

### Fix Applied
- Separated realtime score computation from digest snapshot score
- Introduced immutable “digest snapshot score”
- API now always reads realtime computation layer only

### Impact
- Inconsistent engagement score between API and email digests
- Confusion in reporting and analytics
- Data integrity mismatch between services

---

## 📌 Summary of Fix Themes

Across all issues, the main systemic problems were:
- Missing **idempotency in event processing**
- Lack of **proper concurrency control**
- Improper **data scoping (project isolation)**
- Confusion between **realtime vs snapshot data models**
- Missing **business rule implementation in schedulers**