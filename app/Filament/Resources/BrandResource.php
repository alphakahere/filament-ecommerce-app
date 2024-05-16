<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Brand;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Filament\Resources\Resource;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Section;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\MarkdownEditor;
use App\Filament\Resources\BrandResource\Pages;
use Filament\Tables\Filters\SelectFilter;

class BrandResource extends Resource
{
    protected static ?string $model = Brand::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';


    protected static ?int $navigationSort = 3;

    protected static ?string $navigationGroup = 'Shop';

    protected static ?string $recordTitleAttribute = 'name';

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
                          ->disabled()
                          ->dehydrated(),
                        
                          TextInput::make('url')
                          ->url()
                          ->nullable(),
                      
  
                      MarkdownEditor::make('description'),
                      
                    ]),
                ]),
                Group::make()->schema([
                    
                    Section::make('Status')->schema([
                        Toggle::make('is_visible')
                        ->label('Visibility')
                        ->helperText('Enable or disable category visibility')
                        ->default(true),
                    ])
                ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->toggleable()->default(false),
                TextColumn::make('name')->searchable(),
                TextColumn::make('slug')->toggleable(isToggledHiddenByDefault:true),
                IconColumn::make('is_visible')->boolean()->sortable(),
                TextColumn::make('url'),
                TextColumn::make('description')->toggleable(isToggledHiddenByDefault:true),
            ])
            ->filters([
              SelectFilter::make('Visibilty')->options([
                'true' => 'Visible',
                'false' => 'Hidden'
              ])->attribute('is_visible')
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
            'index' => Pages\ListBrands::route('/'),
            'create' => Pages\CreateBrand::route('/create'),
            'edit' => Pages\EditBrand::route('/{record}/edit'),
        ];
    }
}
