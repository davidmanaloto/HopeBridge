package com.example.hopebridge

data class LoginResponse(
    val status: String,
    val id: Int?,
    val username: String?,
    val email: String?,
    val message: String?
)
