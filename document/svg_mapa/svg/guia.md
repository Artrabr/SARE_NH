para arrumar os caminhos de volta a relativos:
sed -i -E 's|href="[^"]*/salas\.php|href="./salas.php|g' stable_optimized_relative_path_3_5.svg
