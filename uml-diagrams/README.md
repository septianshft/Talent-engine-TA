# UML Diagrams - TalentConnect System

This folder contains comprehensive UML diagrams for the TalentConnect talent management system, designed using PlantUML format. These diagrams provide visual documentation of the system architecture, workflows, and component relationships.

## 📋 Diagram Overview

### 1. Class Diagram (`class-diagram.puml`)
**Purpose**: Shows the static structure of the system including models, services, controllers, and their relationships.

**Key Components**:
- **Models**: User, Role, TalentRequest, Competency, CompetencyTalentRequest
- **Services**: EnhancedDecisionSupportService (SAW algorithm implementation)
- **Controllers**: TalentRequestController, AdminTalentRequestController
- **Pivot Tables**: role_user, competency_user, talent_request_assignments

**Notable Features**:
- Complete relationship mapping (One-to-Many, Many-to-Many)
- Method signatures for key operations
- Service dependencies and usage patterns
- Detailed notes on status values and weight ranges

### 2. Sequence Diagram (`sequence-diagram.puml`)
**Purpose**: Illustrates the dynamic behavior and interaction flow between system components during talent request processes.

**Covered Workflows**:
- **Regular Talent Request Flow**: User creates request → Admin reviews → DSS processing → Assignment
- **Direct Talent Request Flow**: User directly requests specific talent → Immediate assignment
- **Admin Review Process**: DSS ranking, talent selection, and assignment
- **Talent Response Process**: Accept/reject assignments
- **Error Handling**: Validation errors, database errors, DSS calculation errors

**Key Interactions**:
- Form validation and request processing
- Enhanced DSS service calculations
- Database transactions and rollback scenarios
- Multi-actor workflow coordination

### 3. Use Case Diagram (`usecase-diagram.puml`)
**Purpose**: Defines system functionality from user perspective, showing what each actor can do within the system.

**Actors**:
- **Requesting User**: Creates and manages talent requests
- **Administrator**: Reviews requests, manages assignments, system oversight
- **Talent**: Responds to assignments, manages availability
- **System**: Automated processes and notifications

**Function Categories**:
- **Authentication & Profile**: Registration, login, profile management
- **Talent Request Management**: Create, edit, delete requests with competency specifications
- **Administrative Functions**: DSS rankings, assignments, system analytics
- **Talent Functions**: View and respond to assignments
- **Discovery Features**: Search and filter talents
- **Decision Support**: Automated ranking and recommendations

### 4. Activity Diagram - DSS (`activity-diagram-dss.puml`)
**Purpose**: Details the Enhanced Decision Support System workflow using SAW (Simple Additive Weighting) algorithm.

**Process Flow**:
1. **Input Validation**: Competency requirements and weight distribution
2. **Talent Filtering**: Role-based filtering with veto thresholds
3. **SAW Algorithm Process**:
   - Competency Scoring (85% weight)
   - Location Scoring (15% weight)
   - Performance normalization
4. **Ranking & Output**: Sorted recommendations with confidence scores
5. **Sensitivity Analysis**: Alternative scenarios and robustness checks

**Key Features**:
- Parallel processing for competency and location scoring
- Veto threshold application (80% rule)
- Confidence score calculation
- Error handling for edge cases

### 5. State Diagram (`state-diagram.puml`)
**Purpose**: Shows the lifecycle states of talent requests and valid transitions between states.

**State Categories**:
- **Draft**: Initial request creation
- **Admin Review Process**: pending_admin, dss_processing
- **Direct Request Process**: pending_talent (direct assignments)
- **Assignment Process**: talent_assigned, multiple talent scenarios
- **Resolution States**: approved, rejected_admin, rejected_talent, completed
- **Error States**: cancelled, expired

**State Transitions**:
- User actions (submit, cancel)
- Admin actions (assign, reject, complete)
- Talent actions (accept, reject)
- System actions (timeout, validation)

## 🛠 How to Use These Diagrams

### Viewing the Diagrams
1. **Online PlantUML Editor**: Copy paste content to [PlantUML Online Editor](http://www.plantuml.com/plantuml/uml/)
2. **VS Code Extension**: Install "PlantUML" extension for direct preview
3. **Local PlantUML**: Install PlantUML locally with Java runtime

### Generating Images
```bash
# Using PlantUML JAR (requires Java)
java -jar plantuml.jar *.puml

# Using VS Code extension
# Right-click on .puml file → "Preview Current PlantUML"
```

### Integration with Documentation
These diagrams are referenced in:
- `docs/Enhanced_DSS_Documentation.md` - Technical implementation details
- `docs/SAW_Implementation_Analysis.md` - Algorithm-specific documentation
- `README.md` - System overview and architecture
- `CHANGELOG.md` - Version history and changes

## 📊 Technical Specifications

**System Version**: 1.2.0 (May 2025)
**Framework**: Laravel 11.x
**Database**: SQLite (development), MySQL/PostgreSQL (production)
**Algorithm**: Enhanced SAW (Simple Additive Weighting)
**Architecture**: MVC with Service Layer

**Key Metrics**:
- **Models**: 5 primary, 3 pivot tables
- **Controllers**: 6 main controllers
- **Services**: 1 core DSS service
- **Use Cases**: 30+ functional requirements
- **States**: 12 distinct request states

## 🔄 Maintenance and Updates

When updating these diagrams:

1. **Model Changes**: Update class diagram relationships and attributes
2. **Workflow Changes**: Modify sequence and activity diagrams
3. **New Features**: Add use cases and state transitions
4. **Algorithm Updates**: Revise DSS activity diagram
5. **Version Control**: Update version notes in diagram headers

## 📝 Notes

- All diagrams use PlantUML's plain theme for consistency
- Diagrams include version information and creation dates
- Color coding and icons enhance readability
- Comments within .puml files explain complex relationships
- Diagrams are designed for both technical and business stakeholders

## 🔗 Related Documentation

- [Enhanced DSS Documentation](../docs/Enhanced_DSS_Documentation.md)
- [SAW Implementation Analysis](../docs/SAW_Implementation_Analysis.md)
- [System Integration Notes](../integration_notes.md)
- [Deployment Guide](../DEPLOYMENT_GUIDE.md)

---

**Last Updated**: May 27, 2025  
**Created By**: System Architecture Team  
**PlantUML Version**: Compatible with v1.2024.x and later
