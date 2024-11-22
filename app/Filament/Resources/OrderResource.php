<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrderResource\Pages;
use App\Filament\Resources\OrderResource\RelationManagers;
use App\Models\Order;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static ?string $navigationIcon = 'heroicon-o-collection';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('event_id')->required(),
                Forms\Components\TextInput::make('event_date')->required(),
                Forms\Components\TextInput::make('ticket_adult_price')->required(),
                Forms\Components\TextInput::make('ticket_adult_quantity')->required(),
                Forms\Components\TextInput::make('ticket_kid_price')->required(),
                Forms\Components\TextInput::make('ticket_kid_quantity')->required(),
                Forms\Components\TextInput::make('barcode')->required()->unique(),
                Forms\Components\TextInput::make('user_id')->required(),
                Forms\Components\TextInput::make('total_price')->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')->sortable(),
                Tables\Columns\TextColumn::make('event_id')->sortable(),
                Tables\Columns\TextColumn::make('event_date')->sortable(),
                Tables\Columns\TextColumn::make('ticket_adult_price'),
                Tables\Columns\TextColumn::make('ticket_adult_quantity'),
                Tables\Columns\TextColumn::make('ticket_kid_price'),
                Tables\Columns\TextColumn::make('ticket_kid_quantity'),
                Tables\Columns\TextColumn::make('barcode'),
                Tables\Columns\TextColumn::make('user_id'),
                Tables\Columns\TextColumn::make('total_price'),
                Tables\Columns\TextColumn::make('created_at')->sortable(),
                Tables\Columns\TextColumn::make('updated_at')->sortable(),
            ])
            ->filters([
                // Add any filters here
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOrders::route('/'),
            'create' => Pages\CreateOrder::route('/create'),
            'edit' => Pages\EditOrder::route('/{record}/edit'),
        ];
    }
}
