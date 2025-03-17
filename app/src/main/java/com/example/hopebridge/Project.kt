package com.example.hopebridge

data class Project(
    val projectName: String,
    val projectSummary: String,
    val imageUrl: String?,
    val funds_raised: Double,
    val donationGoal: String,
    val organizationId: Int
)



