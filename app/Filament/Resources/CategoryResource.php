<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CategoryResource\Pages;
use App\Models\Category;
use Filament\Forms;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use illuminate\Support\Str;

class CategoryResource extends Resource
{
    protected static ?string $model = Category::class;

    protected static ?string $navigationIcon = 'heroicon-o-tag';


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Group::make()->schema([
                    Section::make()->schema([
                      TextInput::make('name')
                          ->required()
                          ->minLength(4)
                          ->live(onBlur:true)
                          ->afterStateUpdated(function (string $operation, $state, Forms\Set $set) {
                              if($operation !== 'create' && $operation !== 'edit') {
                                  return ;
                              }
                              $set('slug', Str::slug($state));
                          }),
  
                      TextInput::make('slug')
                          ->required()
                          ->unique()
                          ->disabled()
                          ->dehydrated(),
  
                      MarkdownEditor::make('description')->columnSpan('full'),
                      
                    ])->columns(2),
                  ])  ,
                  Group::make()->schema([
                      Section::make('Status')->schema([
  
                        Toggle::make('is_visible')
                        ->label('Visibility')
                        ->helperText('Enable or disable category visibility')
                        ->default(true)->columnSpan('full'),
  
                        Select::make('parent_id')->relationship('parent', 'name')->nullable(),
    
                        
                      ]),
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->sortable()->toggleable(isToggledHiddenByDefault:true),
                TextColumn::make('name')->searchable()->sortable(),
                TextColumn::make('parent.name')->label("Parent")->sortable()->searchable(),
                TextColumn::make('slug')->toggleable(isToggledHiddenByDefault:true),
                IconColumn::make('is_visible')->boolean()->sortable(),
                TextColumn::make('description'),
                TextColumn::make('created_at')->date()->sortable(),
            ])
            ->filters([
                SelectFilter::make('parent')->relationship('parent', 'name')
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCategories::route('/'),
            'create' => Pages\CreateCategory::route('/create'),
            'edit' => Pages\EditCategory::route('/{record}/edit'),
        ];
    }
}
