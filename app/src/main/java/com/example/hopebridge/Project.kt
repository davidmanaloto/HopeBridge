package com.example.hopebridge

data class Project(
    val project_id: Int,
    val user_id: Int,
    val project_name: String,
    val project_summary: String,
    val image_url: String?
)

