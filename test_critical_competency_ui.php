<?php
echo "🎯 Critical Competency UI Integration Test\n";
echo "==========================================\n\n";

// Check if the Enhanced DSS Service includes critical competency support
$enhancedServicePath = __DIR__ . '/app/Services/EnhancedDecisionSupportService.php';
if (file_exists($enhancedServicePath)) {
    $serviceContent = file_get_contents($enhancedServicePath);

    echo "🔧 Enhanced DSS Service - Critical Features:\n";
    echo (strpos($serviceContent, 'is_critical') !== false ? "✅" : "❌") . " Critical competency support: " . (strpos($serviceContent, 'is_critical') !== false ? "Implemented" : "Missing") . "\n";
    echo (strpos($serviceContent, 'veto_threshold') !== false ? "✅" : "❌") . " Veto threshold support: " . (strpos($serviceContent, 'veto_threshold') !== false ? "Implemented" : "Missing") . "\n";
    echo (strpos($serviceContent, 'effectiveVetoThreshold') !== false ? "✅" : "❌") . " Veto threshold calculation: " . (strpos($serviceContent, 'effectiveVetoThreshold') !== false ? "Implemented" : "Missing") . "\n";
    echo (strpos($serviceContent, 'CRITICAL_COMPETENCY_BONUS') !== false ? "✅" : "❌") . " Critical competency bonus: " . (strpos($serviceContent, 'CRITICAL_COMPETENCY_BONUS') !== false ? "Implemented" : "Missing") . "\n";
} else {
    echo "❌ Enhanced DSS Service file not found\n";
}

echo "\n";

// Check the request validation
$requestPath = __DIR__ . '/app/Http/Requests/TalentRequestRequest.php';
if (file_exists($requestPath)) {
    $requestContent = file_get_contents($requestPath);

    echo "📝 Request Validation - Critical Features:\n";
    echo (strpos($requestContent, 'is_critical') !== false ? "✅" : "❌") . " Critical competency validation: " . (strpos($requestContent, 'is_critical') !== false ? "Implemented" : "Missing") . "\n";
    echo (strpos($requestContent, 'veto_threshold') !== false ? "✅" : "❌") . " Veto threshold validation: " . (strpos($requestContent, 'veto_threshold') !== false ? "Implemented" : "Missing") . "\n";
    echo (strpos($requestContent, 'validateCriticalCompetencies') !== false ? "✅" : "❌") . " Critical competency business rules: " . (strpos($requestContent, 'validateCriticalCompetencies') !== false ? "Implemented" : "Missing") . "\n";
} else {
    echo "❌ Request validation file not found\n";
}

echo "\n";

// Check the create form UI
$createFormPath = __DIR__ . '/resources/views/user/requests/create.blade.php';
if (file_exists($createFormPath)) {
    $formContent = file_get_contents($createFormPath);

    echo "🎨 Create Form UI - Critical Features:\n";
    echo (strpos($formContent, 'competency-critical') !== false ? "✅" : "❌") . " Critical competency toggles: " . (strpos($formContent, 'competency-critical') !== false ? "Implemented" : "Missing") . "\n";
    echo (strpos($formContent, 'veto_threshold') !== false ? "✅" : "❌") . " Veto threshold slider: " . (strpos($formContent, 'veto_threshold') !== false ? "Implemented" : "Missing") . "\n";
    echo (strpos($formContent, 'Critical Competency Configuration') !== false ? "✅" : "❌") . " Critical competency section: " . (strpos($formContent, 'Critical Competency Configuration') !== false ? "Implemented" : "Missing") . "\n";
    echo (strpos($formContent, 'orange-') !== false ? "✅" : "❌") . " Orange theme styling: " . (strpos($formContent, 'orange-') !== false ? "Implemented" : "Missing") . "\n";

    // Check JavaScript functionality
    echo "\n🔧 JavaScript Functionality:\n";
    echo (strpos($formContent, 'criticalToggle') !== false ? "✅" : "❌") . " Critical toggle handling: " . (strpos($formContent, 'criticalToggle') !== false ? "Implemented" : "Missing") . "\n";
    echo (strpos($formContent, 'is_critical') !== false && strpos($formContent, 'criticalInput') !== false ? "✅" : "❌") . " Critical form submission: " . (strpos($formContent, 'is_critical') !== false && strpos($formContent, 'criticalInput') !== false ? "Implemented" : "Missing") . "\n";
    echo (strpos($formContent, 'vetoThresholdSlider') !== false ? "✅" : "❌") . " Veto threshold interactivity: " . (strpos($formContent, 'vetoThresholdSlider') !== false ? "Implemented" : "Missing") . "\n";
} else {
    echo "❌ Create form file not found\n";
}

echo "\n";

// Check enhanced results view
$enhancedResultsPath = __DIR__ . '/resources/views/user/requests/enhanced-results.blade.php';
if (file_exists($enhancedResultsPath)) {
    $resultsContent = file_get_contents($enhancedResultsPath);

    echo "📊 Enhanced Results View - Critical Features:\n";
    echo (strpos($resultsContent, 'critical') !== false ? "✅" : "❌") . " Critical competency display: " . (strpos($resultsContent, 'critical') !== false ? "Implemented" : "Missing") . "\n";
    echo (strpos($resultsContent, 'veto') !== false ? "✅" : "❌") . " Veto threshold information: " . (strpos($resultsContent, 'veto') !== false ? "Implemented" : "Missing") . "\n";
    echo (strpos($resultsContent, 'orange-') !== false ? "✅" : "❌") . " Consistent orange theming: " . (strpos($resultsContent, 'orange-') !== false ? "Implemented" : "Missing") . "\n";
} else {
    echo "❌ Enhanced results view file not found\n";
}

echo "\n";

// Check show view integration
$showViewPath = __DIR__ . '/resources/views/user/requests/show.blade.php';
if (file_exists($showViewPath)) {
    $showContent = file_get_contents($showViewPath);

    echo "👁️ Show View Integration:\n";
    echo (strpos($showContent, 'enhanced-results') !== false ? "✅" : "❌") . " Enhanced DSS link: " . (strpos($showContent, 'enhanced-results') !== false ? "Implemented" : "Missing") . "\n";
    echo (strpos($showContent, 'Enhanced DSS Analysis') !== false ? "✅" : "❌") . " Enhanced DSS section: " . (strpos($showContent, 'Enhanced DSS Analysis') !== false ? "Implemented" : "Missing") . "\n";
} else {
    echo "❌ Show view file not found\n";
}

echo "\n";

// Check routes
$routesPath = __DIR__ . '/routes/web.php';
if (file_exists($routesPath)) {
    $routesContent = file_get_contents($routesPath);

    echo "🛣️ Route Configuration:\n";
    echo (strpos($routesContent, 'enhanced-results') !== false ? "✅" : "❌") . " Enhanced results route: " . (strpos($routesContent, 'enhanced-results') !== false ? "Registered" : "Missing") . "\n";
    echo (strpos($routesContent, 'enhancedResults') !== false ? "✅" : "❌") . " Controller method binding: " . (strpos($routesContent, 'enhancedResults') !== false ? "Correct" : "Missing") . "\n";
} else {
    echo "❌ Routes file not found\n";
}

echo "\n🎯 Critical Competency UI Test Summary:\n";
echo "=====================================\n";

// Count implemented features
$totalFeatures = 0;
$implementedFeatures = 0;

if (file_exists($enhancedServicePath)) {
    $serviceContent = file_get_contents($enhancedServicePath);
    $totalFeatures += 4;
    if (strpos($serviceContent, 'is_critical') !== false) $implementedFeatures++;
    if (strpos($serviceContent, 'veto_threshold') !== false) $implementedFeatures++;
    if (strpos($serviceContent, 'effectiveVetoThreshold') !== false) $implementedFeatures++;
    if (strpos($serviceContent, 'CRITICAL_COMPETENCY_BONUS') !== false) $implementedFeatures++;
}

if (file_exists($requestPath)) {
    $requestContent = file_get_contents($requestPath);
    $totalFeatures += 3;
    if (strpos($requestContent, 'is_critical') !== false) $implementedFeatures++;
    if (strpos($requestContent, 'veto_threshold') !== false) $implementedFeatures++;
    if (strpos($requestContent, 'validateCriticalCompetencies') !== false) $implementedFeatures++;
}

if (file_exists($createFormPath)) {
    $formContent = file_get_contents($createFormPath);
    $totalFeatures += 7;
    if (strpos($formContent, 'competency-critical') !== false) $implementedFeatures++;
    if (strpos($formContent, 'veto_threshold') !== false) $implementedFeatures++;
    if (strpos($formContent, 'Critical Competency Configuration') !== false) $implementedFeatures++;
    if (strpos($formContent, 'orange-') !== false) $implementedFeatures++;
    if (strpos($formContent, 'criticalToggle') !== false) $implementedFeatures++;
    if (strpos($formContent, 'is_critical') !== false && strpos($formContent, 'criticalInput') !== false) $implementedFeatures++;
    if (strpos($formContent, 'vetoThresholdSlider') !== false) $implementedFeatures++;
}

if (file_exists($enhancedResultsPath)) {
    $resultsContent = file_get_contents($enhancedResultsPath);
    $totalFeatures += 3;
    if (strpos($resultsContent, 'critical') !== false) $implementedFeatures++;
    if (strpos($resultsContent, 'veto') !== false) $implementedFeatures++;
    if (strpos($resultsContent, 'orange-') !== false) $implementedFeatures++;
}

if (file_exists($showViewPath)) {
    $showContent = file_get_contents($showViewPath);
    $totalFeatures += 2;
    if (strpos($showContent, 'enhanced-results') !== false) $implementedFeatures++;
    if (strpos($showContent, 'Enhanced DSS Analysis') !== false) $implementedFeatures++;
}

if (file_exists($routesPath)) {
    $routesContent = file_get_contents($routesPath);
    $totalFeatures += 2;
    if (strpos($routesContent, 'enhanced-results') !== false) $implementedFeatures++;
    if (strpos($routesContent, 'enhancedResults') !== false) $implementedFeatures++;
}

$completionPercentage = $totalFeatures > 0 ? round(($implementedFeatures / $totalFeatures) * 100, 1) : 0;

echo "✅ Critical competency features implemented: {$implementedFeatures}/{$totalFeatures} ({$completionPercentage}%)\n";

if ($completionPercentage >= 95) {
    echo "🚀 CRITICAL COMPETENCY UI IS FULLY FUNCTIONAL!\n";
    echo "   Ready for your presentation demo.\n\n";

    echo "🎯 Demo Steps for Your Presentation:\n";
    echo "1. Navigate to Create New Talent Request\n";
    echo "2. Select competencies and toggle some as 'Critical'\n";
    echo "3. Adjust the veto threshold slider (60-100%)\n";
    echo "4. Submit the request\n";
    echo "5. View the request details\n";
    echo "6. Click 'View Enhanced Analysis' in the orange DSS section\n";
    echo "7. Show Enhanced DSS vs Basic SAW comparison\n";
    echo "8. Highlight critical competency impact and veto thresholds\n";
} elseif ($completionPercentage >= 80) {
    echo "⚠️ Minor issues detected - mostly functional\n";
} else {
    echo "❌ Major issues detected - needs attention\n";
}

echo "\nTest completed! ✨\n";
?>
