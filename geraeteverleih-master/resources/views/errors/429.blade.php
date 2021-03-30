@extends('errors.app')
@section('title', __($exception->getStatusCode()))
@section('code', __($exception->getStatusCode()))
@section('message', __($exception->getMessage() ?: 'Not Found'))
