
# PortFlow — Architecture & Delivery Plan

PortFlow is a modular **Port Operations Management Platform**, built as a fully decoupled **SPA**, with a domain-oriented backend and a strong focus on clean code, testability, and scalability.

This document describes:
- System architecture
- Core business rules
- Technology stack
- Testing strategy
- Incremental delivery plan

---

## 1. Architecture Overview

PortFlow follows a **SPA + API** architecture, with frontend and backend completely decoupled and independently versioned.

```
┌────────────────┐        HTTP / JSON        ┌────────────────────┐
│   Vue 3 SPA    │  ─────────────────────▶  │   Laravel API      │
│  (Frontend)    │                          │   (Backend)        │
└────────────────┘  ◀─────────────────────  └────────────────────┘
      Auth Token / Sanctum        PostgreSQL | Redis | S3
```

### Architectural Principles
- Clear separation of concerns
- Backend focused on business rules
- Frontend focused on user experience
- Horizontally scalable architecture
- Cloud-ready infrastructure

---

## 2. Backend Architecture

### 2.1 Architectural Style
- RESTful API
- Modular architecture (Delivery by Feature)
- DDD Light approach
- SOLID principles
- Clearly defined layers

### 2.2 Module Organization

Each feature is encapsulated in an independent module:

```
app/
└── Modules/
    ├── Users/
    ├── Ships/
    ├── Berths/
    ├── Containers/
    ├── Operations/
    └── Billing/
```

Each module contains:
- **Domain** → Entities, business rules, domain services
- **Application** → Use cases
- **Infrastructure** → Repositories and external integrations
- **Http** → Controllers and requests
- **Routes** → Module-specific API routes

This structure enables:
- Independent evolution of features
- Isolated testing
- Future extraction into microservices

---

## 3. Core Business Rules

### 3.1 Ships
- Ships have ETA and ETD
- A ship can only dock if a berth is available
- A ship cannot be finalized while operations are still pending

### 3.2 Berths
- A berth can be:
  - Available
  - Occupied
  - Under maintenance
- An occupied berth cannot accept another ship
- Occupancy history is recorded

### 3.3 Containers
- Identified by ISO container code
- Types: 20ft, 40ft, Reefer
- Lifecycle states:
  - Awaiting unloading
  - Unloaded
  - In yard
  - Released
- Invalid state transitions are blocked at the domain level

### 3.4 Operations
- Operations represent logistics events:
  - Unloading
  - Loading
  - Internal movements
- Each operation emits domain events
- Events trigger:
  - Status updates
  - Historical records
  - Asynchronous processing

### 3.5 Billing (Future Phase)
- Fee calculation for:
  - Storage
  - Demurrage
  - Energy consumption (Reefer containers)
- Rules based on time and cargo type

---

## 4. Frontend Architecture (SPA)

### Technology Stack
- Vue 3
- Vite
- Pinia
- Vue Router
- Axios

### Structure
```
src/
├── modules/
│   ├── containers/
│   ├── ships/
│   └── operations/
├── layouts/
├── services/
├── stores/
└── router/
```

### Frontend Principles
- Feature-based component organization
- Centralized state management
- No business logic inside components
- API-driven communication

---

## 5. Authentication & Security

- Authentication via **Laravel Sanctum**
- Token-based SPA authentication
- Role-based access control
- Policies enforced at domain and application levels

---

## 6. Testing Strategy

### Backend
- **Unit Tests**
  - Focus on business rules
  - Domain services and invariants
- **Feature Tests**
  - API contracts
  - Authentication and authorization
- **Event & Job Tests**
  - Asynchronous processing validation

Test database:
- SQLite in-memory

### Frontend
- Vitest
- Tests for:
  - Pinia stores
  - Critical UI components

---

## 7. Infrastructure & DevOps

### Docker
- Isolated containers:
  - PHP
  - Nginx
  - PostgreSQL
  - Redis
- Standardized development environment

### AWS (Free Tier)
- EC2 → Backend API
- RDS PostgreSQL → Database
- S3 → Assets and reports
- CloudWatch → Logs and monitoring
- IAM → Access control

---

## 8. Delivery Plan

### Phase 1 — Foundation
- Laravel and Vue setup
- Local Docker environment
- Users module
- Authentication
- Base test setup

### Phase 2 — Port Core
- Ships
- Berths
- Containers
- Basic operations
- Initial dashboard

### Phase 3 — Events & History
- Domain events and jobs
- Operations timeline
- Auditable history

### Phase 4 — Advanced Dashboard
- Metrics and KPIs
- Filters
- Periodic updates

### Phase 5 — Billing
- Fee calculation rules
- Cost simulator
- Reports generation

### Phase 6 — Cloud Deployment
- AWS deployment
- Infrastructure documentation

---

## 9. Project Purpose

PortFlow is designed not only as a functional system, but as a practical demonstration of:
- Clean architecture
- Sustainable codebases
- Complex business domains
- Modern development best practices

---

**PortFlow**  
_Modular Port Operations Management Platform_
