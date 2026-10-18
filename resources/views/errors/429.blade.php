@extends('errors.branded')

@section('title', 'Too many requests')
@section('code', '429')
@section('heading', 'Too many requests')
@section('message', 'You have made too many requests in a short time. Please wait a moment and try again.')